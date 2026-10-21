<?php

namespace Webkul\Project\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Security\Support\AdminAccess;

class OperationalScope
{
    /**
     * Project IDs the current (or given) user should see for OM-scoped widgets.
     * Admin/HRD: all active projects. OM: projects they manage or are assigned to.
     * Others: projects they manage/are assigned to / follow via employee link.
     *
     * @return array<int, int>
     */
    public function projectIds(?User $user = null): array
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! Schema::hasTable('projects_projects')) {
            return [];
        }

        if (AdminAccess::check($user) || (method_exists($user, 'hasRole') && $user->hasRole('HRD'))) {
            return [];
        }

        // A Team Leader only ever sees the one project they lead.
        if ($this->isTeamLeaderOnly($user)) {
            return $this->teamLeaderProjectIds($user);
        }

        $ids = collect();

        $ids = $ids->merge(
            Project::query()
                ->withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->pluck('id')
        );

        if (Schema::hasTable('projects_project_employees') && Schema::hasTable('employees_employees')) {
            $employeeId = Employee::query()
                ->where('user_id', $user->id)
                ->value('id');

            if ($employeeId) {
                $ids = $ids->merge(
                    DB::table('projects_project_employees')
                        ->where('employee_id', $employeeId)
                        ->pluck('project_id')
                );
            }
        }

        return $ids->map(fn ($id): int => (int) $id)->unique()->values()->all();
    }

    /**
     * Project IDs where the user (via their employee record) is Team Leader.
     *
     * @return array<int, int>
     */
    public function teamLeaderProjectIds(?User $user = null): array
    {
        $user ??= auth()->user();

        if (! $user instanceof User
            || ! Schema::hasTable('projects_projects')
            || ! Schema::hasColumn('projects_projects', 'team_leader_employee_id')
            || ! Schema::hasTable('employees_employees')) {
            return [];
        }

        $employeeId = Employee::query()
            ->where('user_id', $user->id)
            ->value('id');

        if (! $employeeId) {
            return [];
        }

        return Project::query()
            ->withoutGlobalScopes()
            ->where('team_leader_employee_id', $employeeId)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * True when the user is a Team Leader without a broader operational
     * role (Admin/HRD/Operation Manager keep their wider scope).
     */
    public function isTeamLeaderOnly(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! method_exists($user, 'hasRole')) {
            return false;
        }

        if (! $user->hasRole('Team Leader') || $user->hasRole('Operation Manager')) {
            return false;
        }

        return ! $this->isUnscoped($user);
    }

    /**
     * Team IDs a scoped user may see: teams of projects in their scope
     * (managed or member), teams they created, and teams they belong to.
     *
     * @return array<int, int>
     */
    public function visibleTeamIds(?User $user = null): array
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! Schema::hasTable('teams')) {
            return [];
        }

        $ids = collect();

        $projectIds = $this->projectIds($user);

        if ($projectIds !== [] && Schema::hasColumn('teams', 'project_id')) {
            $ids = $ids->merge(
                Team::query()
                    ->withoutGlobalScopes()
                    ->whereIn('project_id', $projectIds)
                    ->pluck('id')
            );
        }

        $ids = $ids->merge(
            Team::query()
                ->withoutGlobalScopes()
                ->where('creator_id', $user->id)
                ->pluck('id')
        );

        if (Schema::hasTable('user_team')) {
            $ids = $ids->merge(
                DB::table('user_team')
                    ->where('user_id', $user->id)
                    ->pluck('team_id')
            );
        }

        return $ids->map(fn ($id): int => (int) $id)->unique()->values()->all();
    }

    /**
     * Team IDs a scoped user may modify: teams of projects they manage
     * as Project Manager, plus teams they created themselves. Narrower
     * than the view scope on purpose (member projects are view-only).
     *
     * @return array<int, int>
     */
    public function editableTeamIds(?User $user = null): array
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! Schema::hasTable('teams')) {
            return [];
        }

        $ids = collect();

        $managedProjectIds = $this->managedProjectIds($user);

        if ($managedProjectIds !== [] && Schema::hasColumn('teams', 'project_id')) {
            $ids = $ids->merge(
                Team::query()
                    ->withoutGlobalScopes()
                    ->whereIn('project_id', $managedProjectIds)
                    ->pluck('id')
            );
        }

        $ids = $ids->merge(
            Team::query()
                ->withoutGlobalScopes()
                ->where('creator_id', $user->id)
                ->pluck('id')
        );

        return $ids->map(fn ($id): int => (int) $id)->unique()->values()->all();
    }

    /**
     * True when the user is plain Staff without a broader operational
     * role. Their project/task lists are restricted to projects they
     * are assigned to (member or managed by them). Operation Manager
     * and Accounting intentionally keep the full project list.
     */
    public function isStaffOnly(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! method_exists($user, 'hasRole')) {
            return false;
        }

        if (! $user->hasRole('Staff') || $user->hasRole('Operation Manager') || $user->hasRole('Accounting')) {
            return false;
        }

        return ! $this->isUnscoped($user);
    }

    public function isUnscoped(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        return AdminAccess::check($user)
            || (method_exists($user, 'hasRole') && $user->hasRole('HRD'));
    }

    /**
     * @return Collection<int, int>
     */
    public function employeeIdsInScope(?User $user = null): Collection
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! Schema::hasTable('employees_employees')) {
            return collect();
        }

        if ($this->isUnscoped($user)) {
            return collect();
        }

        $projectIds = $this->projectIds($user);

        if ($projectIds === [] || ! Schema::hasTable('projects_project_employees')) {
            return collect();
        }

        return DB::table('projects_project_employees')
            ->whereIn('project_id', $projectIds)
            ->pluck('employee_id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();
    }

    /**
     * Project IDs where the user is the Project Manager (projects.user_id),
     * excluding projects they are merely a team member of.
     *
     * @return array<int, int>
     */
    public function managedProjectIds(?User $user = null): array
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! Schema::hasTable('projects_projects')) {
            return [];
        }

        return Project::query()
            ->withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->pluck('id')
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Employee IDs a scoped user may edit/delete: only employees assigned
     * to projects they manage as Project Manager, plus employees they
     * created themselves (editable before first assignment). Broader than
     * this (member projects, direct reports, self) is view-only.
     *
     * @return Collection<int, int>
     */
    public function editableEmployeeIds(?User $user = null): Collection
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! Schema::hasTable('employees_employees')) {
            return collect();
        }

        $ids = collect();

        $managedProjectIds = $this->managedProjectIds($user);

        if ($managedProjectIds !== [] && Schema::hasTable('projects_project_employees')) {
            $ids = $ids->merge(
                DB::table('projects_project_employees')
                    ->whereIn('project_id', $managedProjectIds)
                    ->pluck('employee_id')
                    ->map(fn ($id): int => (int) $id)
            );
        }

        $ids = $ids->merge(
            Employee::query()
                ->where('creator_id', $user->id)
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
        );

        return $ids->unique()->values();
    }

    /**
     * Employee IDs a scoped (non Admin/HRD) user may manage (CRUD):
     * employees assigned to their projects, themselves, their direct
     * reports, and employees they created themselves (so a just-created
     * employee stays reachable before it is assigned to a project).
     *
     * Callers must check isUnscoped() first: for unscoped users this
     * returns an empty collection meaning "no restriction", not "nobody".
     *
     * @return Collection<int, int>
     */
    public function manageableEmployeeIds(?User $user = null): Collection
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! Schema::hasTable('employees_employees')) {
            return collect();
        }

        $ids = $this->employeeIdsInScope($user);

        $ownEmployeeId = Employee::query()
            ->where('user_id', $user->id)
            ->value('id');

        if ($ownEmployeeId) {
            $ids->push((int) $ownEmployeeId);

            $ids = $ids->merge(
                Employee::query()
                    ->where('parent_id', $ownEmployeeId)
                    ->pluck('id')
                    ->map(fn ($id): int => (int) $id)
            );
        }

        $ids = $ids->merge(
            Employee::query()
                ->where('creator_id', $user->id)
                ->pluck('id')
                ->map(fn ($id): int => (int) $id)
        );

        return $ids->unique()->values();
    }
}
