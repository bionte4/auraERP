<?php

namespace Webkul\Project\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Models\Project;
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
