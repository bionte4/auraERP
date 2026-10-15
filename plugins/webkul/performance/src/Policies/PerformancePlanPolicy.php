<?php

namespace Webkul\Performance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Performance\Models\PerformancePlan;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class PerformancePlanPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_performance_performance::plan');
    }

    public function view(User $user, PerformancePlan $performancePlan): bool
    {
        if (! $user->can('view_performance_performance::plan') && ! $user->can('view_any_performance_performance::plan')) {
            return false;
        }

        return $this->hasAccess($user, $performancePlan, 'employee');
    }

    public function create(User $user): bool
    {
        return $user->can('create_performance_performance::plan');
    }

    public function update(User $user, PerformancePlan $performancePlan): bool
    {
        if (! $user->can('update_performance_performance::plan')) {
            return false;
        }

        return $this->hasAccess($user, $performancePlan, 'employee');
    }

    public function delete(User $user, PerformancePlan $performancePlan): bool
    {
        if (! $user->can('delete_performance_performance::plan')) {
            return false;
        }

        return $this->hasAccess($user, $performancePlan, 'employee');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_performance_performance::plan');
    }
}
