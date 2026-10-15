<?php

namespace Webkul\Security;

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
     * Get user IDs of the current user's groups.
     */
    protected function getCurrentAccessibleUserIds(User $user): array
    {
        $teamIds = $user->teams()->pluck('teams.id')->toArray();

        if (empty($teamIds)) {
            return [$user->id];
        }

        return User::query()
            ->withoutGlobalScopes()
            ->select('users.id')
            ->leftJoin('user_team', 'users.id', '=', 'user_team.user_id')
            ->where(function ($query) use ($teamIds, $user): void {
                $query->whereIn('user_team.team_id', $teamIds)
                    ->orWhere('users.id', $user->id);
            })
            ->pluck('users.id')
            ->unique()
            ->values()
            ->toArray();
    }
}
