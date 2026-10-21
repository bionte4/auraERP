<?php

namespace Webkul\Security\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;

class TeamPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_security_team');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Team $team): bool
    {
        if (! $user->can('view_security_team')) {
            return false;
        }

        $scopeClass = 'Webkul\\Project\\Services\\OperationalScope';

        if (! class_exists($scopeClass)) {
            return true;
        }

        $scope = app($scopeClass);

        if ($scope->isUnscoped($user)) {
            return true;
        }

        return in_array($team->id, $scope->visibleTeamIds($user), true);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_security_team');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Team $team): bool
    {
        if (! $user->can('update_security_team')) {
            return false;
        }

        // Editing membership is narrower than viewing: scoped users may
        // only modify teams of projects they manage (PM) or teams they
        // created themselves — never teams of other people's projects.
        $scopeClass = 'Webkul\\Project\\Services\\OperationalScope';

        if (! class_exists($scopeClass)) {
            return true;
        }

        $scope = app($scopeClass);

        if ($scope->isUnscoped($user)) {
            return true;
        }

        return in_array($team->id, $scope->editableTeamIds($user), true);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Team $team): bool
    {
        return $user->can('delete_security_team');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_security_team');
    }
}
