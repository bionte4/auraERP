<?php

namespace Webkul\Project\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Team;

class TeamMembershipSynchronizer
{
    /**
     * Mirror a project's roster (Project Manager + assigned employees)
     * into the team linked to it (explicit project_id, else same name,
     * case-insensitive). Creates the team when missing. Accounts backed
     * by an employee record mirror the roster exactly (unassigned
     * employees are removed again); accounts without an employee
     * record (manual additions) are never removed by sync.
     *
     * @return array{team: ?Team, attached: int, detached: int}
     */
    public function syncForProject(Project $project): array
    {
        if (! $this->canSync()) {
            return ['team' => null, 'attached' => 0, 'detached' => 0];
        }

        $name = trim((string) $project->name);

        if ($name === '') {
            return ['team' => null, 'attached' => 0, 'detached' => 0];
        }

        $team = $this->resolveTeamForProject($project, $name);

        if (! $team instanceof Team) {
            return ['team' => null, 'attached' => 0, 'detached' => 0];
        }

        $result = $this->mirrorUsers($team, $this->collectUserIdsForProject($project));

        return ['team' => $team, 'attached' => $result['attached'], 'detached' => $result['detached']];
    }

    /**
     * Mirror members from every project resolving to this team
     * (explicit link preferred, else same-name projects). Teams that
     * resolve to no project are manual and left untouched.
     *
     * @return array{projects: int, attached: int, detached: int}
     */
    public function syncForTeam(Team $team): array
    {
        if (! $this->canSync()) {
            return ['projects' => 0, 'attached' => 0, 'detached' => 0];
        }

        $projects = $this->projectsForTeam($team);

        if ($projects->isEmpty()) {
            return ['projects' => 0, 'attached' => 0, 'detached' => 0];
        }

        $userIds = [];

        foreach ($projects as $project) {
            $userIds = array_merge($userIds, $this->collectUserIdsForProject($project));
        }

        $result = $this->mirrorUsers($team, array_values(array_unique($userIds)));

        return [
            'projects' => $projects->count(),
            'attached' => $result['attached'],
            'detached' => $result['detached'],
        ];
    }

    /**
     * Names of employees assigned to the project's team who cannot be
     * synced because they have no Related User (login account).
     *
     * @return array<int, string>
     */
    public function unsyncedEmployeeNamesForProject(Project $project): array
    {
        if (! Schema::hasTable('projects_project_employees') || ! method_exists($project, 'employees')) {
            return [];
        }

        $project->loadMissing('employees');
        $names = [];

        foreach ($project->employees as $employee) {
            $userId = $employee->user_id ?: $employee->pivot?->user_id;

            if (! $userId) {
                $names[] = (string) $employee->name;
            }
        }

        return $names;
    }

    /**
     * @return array<int, string>
     */
    public function unsyncedEmployeeNamesForTeam(Team $team): array
    {
        if (! $this->canSync()) {
            return [];
        }

        $names = [];

        foreach ($this->projectsForTeam($team) as $project) {
            $names = array_merge($names, $this->unsyncedEmployeeNamesForProject($project));
        }

        return array_values(array_unique($names));
    }

    /**
     * Backfill all teams/projects by matching names.
     *
     * @return array{projects: int, teams: int, attached: int, detached: int}
     */
    public function syncAll(): array
    {
        if (! $this->canSync()) {
            return ['projects' => 0, 'teams' => 0, 'attached' => 0, 'detached' => 0];
        }

        $projects = Project::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $attached = 0;
        $detached = 0;
        $teamIds = [];

        foreach ($projects as $project) {
            $result = $this->syncForProject($project);
            $attached += $result['attached'];
            $detached += $result['detached'];

            if ($result['team'] instanceof Team) {
                $teamIds[$result['team']->id] = true;
            }
        }

        return [
            'projects' => $projects->count(),
            'teams'    => count($teamIds),
            'attached' => $attached,
            'detached' => $detached,
        ];
    }

    /**
     * Projects a team mirrors: the explicitly linked project, else
     * every project whose name matches the team name.
     *
     * @return Collection<int, Project>
     */
    protected function projectsForTeam(Team $team): Collection
    {
        if (Schema::hasColumn('teams', 'project_id') && $team->project_id) {
            $linked = Project::query()
                ->withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->whereKey($team->project_id)
                ->first();

            if ($linked instanceof Project) {
                return collect([$linked]);
            }
        }

        $name = trim((string) $team->name);

        if ($name === '') {
            return collect();
        }

        return Project::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
            ->get();
    }

    protected function canSync(): bool
    {
        try {
            return Schema::hasTable('teams')
                && Schema::hasTable('user_team')
                && Schema::hasTable('projects_projects')
                && class_exists(Team::class);
        } catch (Throwable) {
            return false;
        }
    }

    protected function resolveTeamForProject(Project $project, string $name): ?Team
    {
        if (Schema::hasColumn('teams', 'project_id')) {
            $linked = Team::query()
                ->withoutGlobalScopes()
                ->where('project_id', $project->id)
                ->first();

            if ($linked instanceof Team) {
                return $linked;
            }
        }

        $normalized = mb_strtolower($name);

        $team = Team::query()
            ->withoutGlobalScopes()
            ->whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
            ->first();

        if ($team instanceof Team) {
            if (Schema::hasColumn('teams', 'project_id') && ! $team->project_id) {
                $team->forceFill(['project_id' => $project->id])->saveQuietly();
            }

            return $team;
        }

        $attributes = ['name' => $name];

        if (Schema::hasColumn('teams', 'project_id')) {
            $attributes['project_id'] = $project->id;
        }

        return Team::query()->create($attributes);
    }

    /**
     * @return array<int, int>
     */
    protected function collectUserIdsForProject(Project $project): array
    {
        $userIds = [];

        if ($project->user_id) {
            $userIds[] = (int) $project->user_id;
        }

        if (method_exists($project, 'employees') && Schema::hasTable('projects_project_employees')) {
            $project->loadMissing('employees');

            foreach ($project->employees as $employee) {
                $userId = $employee->user_id ?: $employee->pivot?->user_id;

                if ($userId) {
                    $userIds[] = (int) $userId;
                }
            }
        }

        return array_values(array_unique(array_filter($userIds)));
    }

    /**
     * Mirror the team's employee-backed accounts to exactly $userIds,
     * then attach any missing ones. Accounts without an employee
     * record are manual additions and are kept as-is.
     *
     * @param  array<int, int>  $userIds
     * @return array{attached: int, detached: int}
     */
    protected function mirrorUsers(Team $team, array $userIds): array
    {
        $current = $team->users()->pluck('users.id')->map(fn ($id): int => (int) $id)->all();

        $employeeBacked = [];

        if ($current !== [] && class_exists(Employee::class) && Schema::hasTable('employees_employees')) {
            $employeeBacked = Employee::query()
                ->whereIn('user_id', $current)
                ->pluck('user_id')
                ->map(fn ($id): int => (int) $id)
                ->all();
        }

        $toDetach = array_values(array_diff(array_intersect($current, $employeeBacked), $userIds));

        if ($toDetach !== []) {
            $team->users()->detach($toDetach);
        }

        $before = $team->users()->pluck('users.id')->map(fn ($id): int => (int) $id)->all();
        $team->users()->syncWithoutDetaching($userIds);
        $after = $team->users()->pluck('users.id')->map(fn ($id): int => (int) $id)->all();

        return [
            'attached' => count(array_diff($after, $before)),
            'detached' => count($toDetach),
        ];
    }
}
