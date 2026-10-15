<?php

namespace Webkul\Security\Traits;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

trait HasScopedPermissions
{
    /**
     * Check if the user has global access to any resource.
     */
    protected function hasGlobalAccess(User $user): bool
    {
        $permission = $user->resource_permission instanceof PermissionType
            ? $user->resource_permission
            : PermissionType::tryFrom((string) $user->resource_permission);

        return $permission === PermissionType::GLOBAL;
    }

    /**
     * Check if the user has group/hierarchical access to resources of users in the same team or org subtree.
     */
    protected function hasGroupAccess(User $user, Model $model, string $ownerAttribute = 'user'): bool
    {
        $permission = $user->resource_permission instanceof PermissionType
            ? $user->resource_permission
            : PermissionType::tryFrom((string) $user->resource_permission);

        if ($permission !== PermissionType::GROUP) {
            return false;
        }

        if (method_exists($model, 'canBeApprovedBy') && $model->canBeApprovedBy($user)) {
            return true;
        }

        if ($model instanceof Employee && $this->isEmployeeSubordinateOfUser($model, $user)) {
            return true;
        }

        $ownerUserIds = $this->resolveModelOwnerUserIds($model, $ownerAttribute);

        if (empty($ownerUserIds)) {
            return false;
        }

        $accessibleUserIds = $this->resolveAccessibleUserIdsFor($user);

        return ! empty(array_intersect($ownerUserIds, $accessibleUserIds));
    }

    /**
     * Check if the user has individual access to their own resources only.
     */
    protected function hasIndividualAccess(User $user, Model $model, string $ownerAttribute = 'user'): bool
    {
        $permission = $user->resource_permission instanceof PermissionType
            ? $user->resource_permission
            : PermissionType::tryFrom((string) $user->resource_permission);

        if ($permission !== PermissionType::INDIVIDUAL) {
            return false;
        }

        if (method_exists($model, 'canBeApprovedBy') && $model->canBeApprovedBy($user)) {
            return true;
        }

        $ownerUserIds = $this->resolveModelOwnerUserIds($model, $ownerAttribute);

        return in_array((int) $user->id, $ownerUserIds, true);
    }

    /**
     * Main access method that combines all permissions.
     */
    protected function hasAccess(User $user, Model $model, string $ownerAttribute = 'user'): bool
    {
        return $this->hasGlobalAccess($user)
            || $this->hasGroupAccess($user, $model, $ownerAttribute)
            || $this->hasIndividualAccess($user, $model, $ownerAttribute);
    }

    /**
     * Resolve all User IDs that own, created, or manage the given model.
     *
     * @return array<int, int>
     */
    protected function resolveModelOwnerUserIds(Model $model, string $ownerAttribute = 'user'): array
    {
        $userIds = [];

        if ($model instanceof User) {
            $userIds[] = (int) $model->id;
        }

        if ($model instanceof Employee) {
            $userIds = array_merge($userIds, $this->extractUserIdsFromEmployee($model));
        }

        try {
            $owner = $model->{$ownerAttribute} ?? null;

            if ($owner instanceof User) {
                $userIds[] = (int) $owner->id;
            } elseif ($owner instanceof Employee) {
                $userIds = array_merge($userIds, $this->extractUserIdsFromEmployee($owner));
            } elseif ($owner instanceof Collection) {
                foreach ($owner as $item) {
                    if ($item instanceof User) {
                        $userIds[] = (int) $item->id;
                    } elseif ($item instanceof Employee) {
                        $userIds = array_merge($userIds, $this->extractUserIdsFromEmployee($item));
                    } elseif (isset($item->user_id)) {
                        $userIds[] = (int) $item->user_id;
                    }
                }
            } elseif (is_object($owner) && isset($owner->user_id)) {
                $userIds[] = (int) $owner->user_id;
            }
        } catch (Throwable) {
            // Safely continue to scalar foreign key fallbacks.
        }

        foreach (['user_id', 'creator_id', 'cashier_id', 'recruiter_id', 'leave_manager_id', 'attendance_manager_id'] as $col) {
            if (! empty($model->{$col})) {
                $userIds[] = (int) $model->{$col};
            }
        }

        if (isset($model->project_id) && $model->project_id && method_exists($model, 'project')) {
            try {
                $projectUserId = $model->project()->withoutGlobalScopes()->value('user_id');

                if ($projectUserId) {
                    $userIds[] = (int) $projectUserId;
                }
            } catch (Throwable) {
            }
        }

        return array_values(array_unique(array_filter($userIds)));
    }

    /**
     * Extract User IDs associated with an Employee record (self, direct manager, coach, leave/attendance managers).
     *
     * @return array<int, int>
     */
    protected function extractUserIdsFromEmployee(Employee $employee): array
    {
        $ids = [];

        foreach (['user_id', 'creator_id', 'leave_manager_id', 'attendance_manager_id'] as $col) {
            if (! empty($employee->{$col})) {
                $ids[] = (int) $employee->{$col};
            }
        }

        if ($employee->parent_id) {
            $parentUserId = Employee::withoutGlobalScopes()->where('id', $employee->parent_id)->value('user_id');

            if ($parentUserId) {
                $ids[] = (int) $parentUserId;
            }
        }

        if ($employee->coach_id) {
            $coachUserId = Employee::withoutGlobalScopes()->where('id', $employee->coach_id)->value('user_id');

            if ($coachUserId) {
                $ids[] = (int) $coachUserId;
            }
        }

        return $ids;
    }

    protected function isEmployeeSubordinateOfUser(Employee $employee, User $user): bool
    {
        try {
            if (! Schema::hasTable('employees_employees')) {
                return false;
            }

            $managerEmployees = Employee::withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->where('user_id', $user->id)
                ->get();

            foreach ($managerEmployees as $managerEmployee) {
                if (in_array((int) $employee->id, $managerEmployee->getSubordinateEmployeeIds(true), true)) {
                    return true;
                }
            }
        } catch (Throwable) {
        }

        return false;
    }

    /**
     * Resolve all user IDs accessible to the given user (via Bouncer if current auth user, or computed directly).
     *
     * @return array<int, int>
     */
    protected function resolveAccessibleUserIdsFor(User $user): array
    {
        if (auth()->id() === $user->id) {
            $bouncerIds = bouncer()->getAuthorizedUserIds();

            if (is_array($bouncerIds)) {
                return array_map(fn ($id) => (int) $id, $bouncerIds);
            }
        }

        $accessibleUserIds = [(int) $user->id];

        $teamIds = $user->teams()->pluck('teams.id')->toArray();

        if (! empty($teamIds)) {
            $teamUserIds = User::query()
                ->withoutGlobalScopes()
                ->select('users.id')
                ->leftJoin('user_team', 'users.id', '=', 'user_team.user_id')
                ->whereIn('user_team.team_id', $teamIds)
                ->pluck('users.id')
                ->map(fn ($id) => (int) $id)
                ->all();

            $accessibleUserIds = array_merge($accessibleUserIds, $teamUserIds);
        }

        try {
            if (class_exists(Employee::class) && Schema::hasTable('employees_employees')) {
                $managerEmployees = Employee::withoutGlobalScopes()
                    ->whereNull('deleted_at')
                    ->where('user_id', $user->id)
                    ->get();

                foreach ($managerEmployees as $managerEmployee) {
                    $accessibleUserIds = array_merge(
                        $accessibleUserIds,
                        $managerEmployee->getSubordinateUserIds(true)
                    );
                }
            }
        } catch (Throwable) {
        }

        return array_values(array_unique($accessibleUserIds));
    }
}
