<?php

namespace Webkul\TimeOff\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;
use Webkul\TimeOff\Models\LeaveAllocation;

class LeaveAllocationPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_time_off_allocation')
            || $user->can('view_any_time_off_my::allocation');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, LeaveAllocation $leaveAllocation): bool
    {
        if (! $user->can('view_time_off_allocation') && ! $user->can('view_time_off_my::allocation')) {
            return false;
        }

        return $this->hasAccess($user, $leaveAllocation, 'employee');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_time_off_allocation')
            || $user->can('create_time_off_my::allocation');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, LeaveAllocation $leaveAllocation): bool
    {
        if (! $user->can('update_time_off_allocation') && ! $user->can('update_time_off_my::allocation')) {
            return false;
        }

        return $this->hasAccess($user, $leaveAllocation, 'employee');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, LeaveAllocation $leaveAllocation): bool
    {
        if (! $user->can('delete_time_off_allocation') && ! $user->can('delete_time_off_my::allocation')) {
            return false;
        }

        return $this->hasAccess($user, $leaveAllocation, 'employee');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_time_off_allocation')
            || $user->can('delete_any_time_off_my::allocation');
    }
}
