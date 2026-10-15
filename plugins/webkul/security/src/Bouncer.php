<?php

namespace Webkul\Security;

use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;

class Bouncer
{
    /**
     * Cached authorized user IDs keyed by user ID and permission type.
     *
     * @var array<string, array<int, int>|null>
     */
    protected static array $authorizedUserIdsCache = [];

    public static function flushCache(): void
    {
        static::$authorizedUserIdsCache = [];
    }

    /**
     * Return user IDs authorized for the current user.
     */
    public function getAuthorizedUserIds(): ?array
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return null;
        }

        $permissionValue = $user->resource_permission instanceof PermissionType
            ? $user->resource_permission->value
            : (string) $user->resource_permission;

        $cacheKey = $user->id.':'.$permissionValue;

        if (array_key_exists($cacheKey, static::$authorizedUserIdsCache)) {
            return static::$authorizedUserIdsCache[$cacheKey];
        }

        if ($user->resource_permission == PermissionType::GLOBAL) {
            static::$authorizedUserIdsCache[$cacheKey] = null;
        } elseif ($user->resource_permission == PermissionType::GROUP) {
            static::$authorizedUserIdsCache[$cacheKey] = $this->getCurrentAccessibleUserIds($user);
        } else {
            static::$authorizedUserIdsCache[$cacheKey] = [$user->id];
        }

        return static::$authorizedUserIdsCache[$cacheKey];
    }

    /**
     * Get user IDs of the current user's teams AND organizational subordinates (reporting line & managed departments).
     */
    protected function getCurrentAccessibleUserIds(User $user): array
    {
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

                $delegatedUserIds = Employee::withoutGlobalScopes()
                    ->whereNull('deleted_at')
                    ->whereNotNull('user_id')
                    ->where(function ($query) use ($user): void {
                        $query->where('leave_manager_id', $user->id)
                            ->orWhere('attendance_manager_id', $user->id);
                    })
                    ->pluck('user_id')
                    ->map(fn ($id) => (int) $id)
                    ->all();

                $accessibleUserIds = array_merge($accessibleUserIds, $delegatedUserIds);
            }
        } catch (Throwable) {
            // Safely ignore if employee tables are not yet migrated.
        }

        return array_values(array_unique($accessibleUserIds));
    }
}
