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
     * True when the user belongs to the finance team (Accounting staff
     * or Accounting Manager). Finance keeps the full project/task list
     * for billing and budget-control context.
     */
    public function hasFinanceRole(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! method_exists($user, 'hasRole')) {
            return false;
        }

        return $user->hasRole('Accounting') || $user->hasRole('Accounting Manager');
    }

    /**
     * True when the user may see a project's contract value
     * (project_value). Company policy: contract values are financial
     * data known only to Admin and the budget-control team
     * (Accounting / Accounting Manager). Operational roles (OM, Team
     * Leader, Staff, HRD, Kasir) work with the operational budget and
     * its realization instead — never the contract value.
     */
    public function canViewProjectValue(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        return AdminAccess::check($user) || $this->hasFinanceRole($user);
    }

    /**
     * True when the model's OwnershipScope global scope must not cut
     * down the Projects/Tasks lists for this user: unscoped roles
     * (Admin/HRD) and finance, whose full list comes from
     * projectListScopeIds() but would otherwise be filtered to records
     * owned by their Resource Permission (Group/Individual) reach.
     */
    public function bypassesListOwnershipScope(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        return $this->isUnscoped($user) || $this->hasFinanceRole($user);
    }

    /**
     * Project IDs the Projects/Tasks lists are restricted to for the
     * given user, or null when the lists stay unrestricted.
     *
     * Admin/HRD are unscoped. Accounting & Accounting Manager keep
     * the full list for billing context unless they also operate as
     * Operation Manager. Team Leaders only see the project they
     * lead; Operation Managers and plain Staff only see projects
     * assigned to them (as Project Manager or via their linked
     * employee).
     *
     * @return array<int, int>|null
     */
    public function projectListScopeIds(?User $user = null): ?array
    {
        $user ??= auth()->user();

        if (! $user instanceof User) {
            return null;
        }

        if ($this->isUnscoped($user)) {
            return null;
        }

        $hasRole = method_exists($user, 'hasRole');

        if ($this->hasFinanceRole($user) && ! ($hasRole && $user->hasRole('Operation Manager'))) {
            return null;
        }

        if ($this->isTeamLeaderOnly($user)) {
            return $this->teamLeaderProjectIds($user);
        }

        if (($hasRole && $user->hasRole('Operation Manager')) || $this->isStaffOnly($user)) {
            return $this->projectIds($user);
        }

        return null;
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

        $projectIds = Project::query()
            ->withoutGlobalScopes()
            ->where('team_leader_employee_id', $employeeId)
            ->pluck('id');

        if (Schema::hasTable('projects_project_units')) {
            $projectIds = $projectIds->merge(
                DB::table('projects_project_units')
                    ->where('team_leader_employee_id', $employeeId)
                    ->pluck('project_id')
            );
        }

        return $projectIds
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

        if ($projectIds !== []) {
            $ids = $ids->merge($this->teamIdsForProjects($projectIds));
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

        if ($managedProjectIds !== []) {
            $ids = $ids->merge($this->teamIdsForProjects($managedProjectIds));
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
     * and finance (Accounting / Accounting Manager) intentionally
     * keep the full project list.
     */
    public function isStaffOnly(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! method_exists($user, 'hasRole')) {
            return false;
        }

        if (! $user->hasRole('Staff') || $user->hasRole('Operation Manager') || $this->hasFinanceRole($user)) {
            return false;
        }

        return ! $this->isUnscoped($user);
    }

    /**
     * Team IDs belonging to the given projects, via the explicit
     * project_id link or — for legacy teams created before that link
     * existed (or left unlinked because a duplicate name won the sync
     * match) — via an exact project-name match on unlinked teams.
     * Mirrors how TeamMembershipSynchronizer resolves teams.
     *
     * @param  array<int, int>  $projectIds
     * @return array<int, int>
     */
    protected function teamIdsForProjects(array $projectIds): array
    {
        if ($projectIds === [] || ! Schema::hasTable('teams')) {
            return [];
        }

        $ids = collect();

        if (Schema::hasColumn('teams', 'project_id')) {
            $ids = $ids->merge(
                Team::query()
                    ->withoutGlobalScopes()
                    ->whereIn('project_id', $projectIds)
                    ->pluck('id')
            );

            $projectNames = Project::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $projectIds)
                ->pluck('name')
                ->map(fn ($name): string => mb_strtolower(trim((string) $name)))
                ->filter(fn (string $name): bool => $name !== '')
                ->unique()
                ->values()
                ->all();

            if ($projectNames !== []) {
                $ids = $ids->merge(
                    Team::query()
                        ->withoutGlobalScopes()
                        ->whereNull('project_id')
                        ->whereIn(DB::raw('LOWER(TRIM(name))'), $projectNames)
                        ->pluck('id')
                );
            }
        }

        return $ids->map(fn ($id): int => (int) $id)->unique()->values()->all();
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
