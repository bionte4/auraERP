<?php

namespace Webkul\Employee\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Employee\Models\EmployeeSkill;
use Webkul\Security\Models\User;

class EmployeeSkillPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_employee_employee::skill')
            || $user->can('view_any_employee_employee');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, EmployeeSkill $employeeSkill): bool
    {
        return $user->can('view_employee_employee::skill')
            || $user->can('view_employee_employee');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_employee_employee::skill')
            || $user->can('update_employee_employee');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, EmployeeSkill $employeeSkill): bool
    {
        return $user->can('update_employee_employee::skill')
            || $user->can('update_employee_employee');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EmployeeSkill $employeeSkill): bool
    {
        return $user->can('delete_employee_employee::skill')
            || $user->can('update_employee_employee');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_employee_employee::skill')
            || $user->can('delete_any_employee_employee');
    }
}
