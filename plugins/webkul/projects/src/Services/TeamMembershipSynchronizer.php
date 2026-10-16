<?php

namespace Webkul\Project\Services;

use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Team;

class TeamMembershipSynchronizer
{
    /**
     * Ensure users from a project's manager + assigned employees are members of a
     * team that shares the same name (case-insensitive). Creates the team when missing.
     *
     * @return array{team: ?Team, attached: int}
     */
    public function syncForProject(Project $project): array
    {
        if (! $this->canSync()) {
            return ['team' => null, 'attached' => 0];
        }

        $name = trim((string) $project->name);

        if ($name === '') {
            return ['team' => null, 'attached' => 0];
        }

        $team = $this->resolveTeamForProject($project, $name);

        if (! $team instanceof Team) {
            return ['team' => null, 'attached' => 0];
        }

        $userIds = $this->collectUserIdsForProject($project);
        $attached = $this->attachUsers($team, $userIds);

        return ['team' => $team, 'attached' => $attached];
    }

    /**
     * Pull members from every project whose name matches the team.
     *
     * @return array{projects: int, attached: int}
     */
    public function syncForTeam(Team $team): array
    {
        if (! $this->canSync()) {
            return ['projects' => 0, 'attached' => 0];
        }

        $projects = collect();

        if (Schema::hasColumn('teams', 'project_id') && $team->project_id) {
            $linked = Project::query()
                ->withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->whereKey($team->project_id)
                ->first();

            if ($linked instanceof Project) {
                $projects = collect([$linked]);
            }
        }

        if ($projects->isEmpty()) {
            $name = trim((string) $team->name);

            if ($name === '') {
                return ['projects' => 0, 'attached' => 0];
            }

            $projects = Project::query()
                ->withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower($name)])
                ->get();
        }

        $userIds = [];

        foreach ($projects as $project) {
            $userIds = array_merge($userIds, $this->collectUserIdsForProject($project));
        }

        $attached = $this->attachUsers($team, array_values(array_unique($userIds)));

        return [
            'projects' => $projects->count(),
            'attached' => $attached,
        ];
    }

    /**
     * Backfill all teams/projects by matching names.
     *
     * @return array{projects: int, teams: int, attached: int}
     */
    public function syncAll(): array
    {
        if (! $this->canSync()) {
            return ['projects' => 0, 'teams' => 0, 'attached' => 0];
        }

        $projects = Project::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->get();

        $attached = 0;
        $teamIds = [];

        foreach ($projects as $project) {
            $result = $this->syncForProject($project);
            $attached += $result['attached'];

            if ($result['team'] instanceof Team) {
                $teamIds[$result['team']->id] = true;
            }
        }

        return [
            'projects' => $projects->count(),
            'teams'    => count($teamIds),
            'attached' => $attached,
        ];
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
     * @param  array<int, int>  $userIds
     */
    protected function attachUsers(Team $team, array $userIds): int
    {
        if ($userIds === []) {
            return 0;
        }

        $before = $team->users()->pluck('users.id')->map(fn ($id): int => (int) $id)->all();
        $team->users()->syncWithoutDetaching($userIds);
        $after = $team->users()->pluck('users.id')->map(fn ($id): int => (int) $id)->all();

        return count(array_diff($after, $before));
    }
}
