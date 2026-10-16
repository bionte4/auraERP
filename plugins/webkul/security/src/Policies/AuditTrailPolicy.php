<?php

namespace Webkul\Security\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\AuditTrail;
use Webkul\Security\Models\User;
use Webkul\Security\Support\AdminAccess;

class AuditTrailPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return AdminAccess::check($user);
    }

    public function view(User $user, AuditTrail $auditTrail): bool
    {
        return AdminAccess::check($user);
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

    public function restore(User $user, AuditTrail $auditTrail): bool
    {
        return false;
    }

    public function restoreAny(User $user): bool
    {
        return false;
    }

    public function forceDelete(User $user, AuditTrail $auditTrail): bool
    {
        return false;
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }
}
