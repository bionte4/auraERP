<?php

namespace Webkul\Security\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\AuditTrail;
use Webkul\Security\Models\User;

class AuditTrailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $this->isAdmin($user);
    }

    public function view(User $user, AuditTrail $auditTrail): bool
    {
        return $this->isAdmin($user);
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, AuditTrail $auditTrail): bool
    {
        return false;
    }

    public function delete(User $user, AuditTrail $auditTrail): bool
    {
        return false;
    }

    public function deleteAny(User $user): bool
    {
        return false;
    }

    protected function isAdmin(User $user): bool
    {
        if (! method_exists($user, 'hasRole')) {
            return false;
        }

        $adminRole = config('filament-shield.panel_user.name', 'Admin');

        return $user->hasRole($adminRole);
    }
}
