<?php

namespace Webkul\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class EmployeePolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_employee_employee');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Employee $employee): bool
    {
        return $user->can('view_employee_employee');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_employee_employee');
    }

    /**
     * Scoped users (e.g. Operation Manager) may only mutate employees on
     * projects they manage as Project Manager (plus employees they created
     * themselves). Membership in a project is view-only. Admin/HRD keep
     * the regular scoped-permission behaviour.
     */
    protected function canMutateInOperationalScope(User $user, Employee $employee): bool
    {
        $scopeClass = 'Webkul\\Project\\Services\\OperationalScope';

        if (! class_exists($scopeClass)) {
            return $this->hasAccess($user, $employee, 'coach');
        }

        $scope = app($scopeClass);

        if ($scope->isUnscoped($user)) {
            return $this->hasAccess($user, $employee, 'coach');
        }

        return $scope->editableEmployeeIds($user)->contains((int) $employee->id);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Employee $employee): bool
    {
        if (! $user->can('update_employee_employee')) {
            return false;
        }

        return $this->canMutateInOperationalScope($user, $employee);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Employee $employee): bool
    {
        if (! $user->can('delete_employee_employee')) {
            return false;
        }

        return $this->canMutateInOperationalScope($user, $employee);
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_employee_employee');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Employee $employee): bool
    {
        if (! $user->can('force_delete_employee_employee')) {
            return false;
        }

        return $this->canMutateInOperationalScope($user, $employee);
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_employee_employee');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Employee $employee): bool
    {
        if (! $user->can('restore_employee_employee')) {
            return false;
        }

        return $this->canMutateInOperationalScope($user, $employee);
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_employee_employee');
    }
}
