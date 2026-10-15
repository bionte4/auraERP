<?php

namespace Webkul\Evaluation\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class EvaluationPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_evaluation_evaluation');
    }

    public function view(User $user, Evaluation $evaluation): bool
    {
        if (! $user->can('view_evaluation_evaluation') && ! $user->can('view_any_evaluation_evaluation')) {
            return $this->isParticipantOrApprover($user, $evaluation);
        }

        return $this->hasAccess($user, $evaluation, 'employee')
            || $this->isParticipantOrApprover($user, $evaluation);
    }

    public function create(User $user): bool
    {
        return $user->can('create_evaluation_evaluation');
    }

    public function update(User $user, Evaluation $evaluation): bool
    {
        if (! $user->can('update_evaluation_evaluation')) {
            return false;
        }

        return $this->hasAccess($user, $evaluation, 'employee')
            || $this->isParticipantOrApprover($user, $evaluation);
    }

    public function delete(User $user, Evaluation $evaluation): bool
    {
        if (! $user->can('delete_evaluation_evaluation')) {
            return false;
        }

        return $this->hasAccess($user, $evaluation, 'employee')
            || $this->isParticipantOrApprover($user, $evaluation);
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_evaluation_evaluation');
    }

    protected function isParticipantOrApprover(User $user, Evaluation $evaluation): bool
    {
        $employeeIds = array_filter([
            $evaluation->employee_id,
            $evaluation->reviewer_id,
            $evaluation->gm_approver_id,
            $evaluation->director_approver_id,
        ]);

        if (empty($employeeIds)) {
            return false;
        }

        return $evaluation->employee()->withoutGlobalScopes()
            ->getModel()
            ->newQueryWithoutScopes()
            ->whereIn('id', $employeeIds)
            ->where('user_id', $user->id)
            ->exists();
    }
}
