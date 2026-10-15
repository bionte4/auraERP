<?php

namespace Webkul\Pos\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Pos\Models\PosSession;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class PosSessionPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pos_pos::session');
    }

    public function view(User $user, PosSession $session): bool
    {
        if (! $user->can('view_pos_pos::session') && ! $user->can('view_any_pos_pos::session')) {
            return false;
        }

        return $this->hasAccess($user, $session, 'user');
    }

    public function create(User $user): bool
    {
        return $user->can('create_pos_pos::session');
    }

    public function update(User $user, PosSession $session): bool
    {
        if (! $user->can('update_pos_pos::session')) {
            return false;
        }

        return $this->hasAccess($user, $session, 'user');
    }

    public function delete(User $user, PosSession $session): bool
    {
        if (! $user->can('delete_pos_pos::session')) {
            return false;
        }

        return $this->hasAccess($user, $session, 'user');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pos_pos::session');
    }
}
