<?php

namespace Webkul\Performance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Performance\Models\PerformancePlan;
use Webkul\Security\Models\User;

class PerformancePlanPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_performance_performance::plan');
    }

    public function view(User $user, PerformancePlan $performancePlan): bool
    {
        return $user->can('view_performance_performance::plan');
    }

    public function create(User $user): bool
    {
        return $user->can('create_performance_performance::plan');
    }

    public function update(User $user, PerformancePlan $performancePlan): bool
    {
        return $user->can('update_performance_performance::plan');
    }

    public function delete(User $user, PerformancePlan $performancePlan): bool
    {
        return $user->can('delete_performance_performance::plan');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_performance_performance::plan');
    }
}
