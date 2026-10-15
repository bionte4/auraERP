<?php

namespace Webkul\TimeOff\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;
use Webkul\TimeOff\Models\Leave;

class LeavePolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_time_off_time::off')
            || $user->can('view_any_time_off_my::time::off')
            || $user->can('view_any_time_off_by::employee');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Leave $leave): bool
    {
        if ($leave->canBeApprovedBy($user)) {
            return true;
        }

        if ($user->can('view_time_off_time::off') || $user->can('view_time_off_by::employee')) {
            return $this->hasAccess($user, $leave, 'employee');
        }

        if ($user->can('view_time_off_my::time::off')) {
            return $this->hasAccess($user, $leave, 'employee');
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_time_off_time::off')
            || $user->can('create_time_off_my::time::off')
            || $user->can('create_time_off_by::employee');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Leave $leave): bool
    {
        $hasPermission = $user->can('update_time_off_time::off')
            || $user->can('update_time_off_my::time::off')
            || $user->can('update_time_off_by::employee');

        if (! $hasPermission && ! $leave->canBeApprovedBy($user)) {
            return false;
        }

        return $leave->canBeApprovedBy($user) || $this->hasAccess($user, $leave, 'employee');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Leave $leave): bool
    {
        $hasPermission = $user->can('delete_time_off_time::off')
            || $user->can('delete_time_off_my::time::off')
            || $user->can('delete_time_off_by::employee');

        if (! $hasPermission) {
            return false;
        }

        return $this->hasAccess($user, $leave, 'employee');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_time_off_time::off')
            || $user->can('delete_any_time_off_my::time::off')
            || $user->can('delete_any_time_off_by::employee');
    }
}
