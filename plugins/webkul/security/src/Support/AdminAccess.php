<?php

namespace Webkul\Security\Support;

use Webkul\Security\Models\User;

final class AdminAccess
{
    /**
     * Permissions that must never be granted to non-Admin roles.
     *
     * @return array<int, string>
     */
    public static function adminOnlyPermissionNames(): array
    {
        return [
            'view_any_security_audit::trail',
            'view_security_audit::trail',
            'page_security_manage_database_backups',
        ];
    }

    public static function adminRoleName(): string
    {
        return config('filament-shield.panel_user.name', 'Admin') ?: 'Admin';
    }

    public static function check(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! method_exists($user, 'hasRole')) {
            return false;
        }

        return $user->hasRole(self::adminRoleName());
    }

    public static function ensure(?User $user = null): void
    {
        abort_unless(self::check($user), 403);
    }
}
