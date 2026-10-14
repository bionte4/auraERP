<?php

namespace Webkul\Evaluation\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Security\Models\User;

class EvaluationPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_evaluation_evaluation');
    }

    public function view(User $user, Evaluation $evaluation): bool
    {
        return $user->can('view_evaluation_evaluation');
    }

    public function create(User $user): bool
    {
        return $user->can('create_evaluation_evaluation');
    }

    public function update(User $user, Evaluation $evaluation): bool
    {
        return $user->can('update_evaluation_evaluation');
    }

    public function delete(User $user, Evaluation $evaluation): bool
    {
        return $user->can('delete_evaluation_evaluation');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_evaluation_evaluation');
    }
}
