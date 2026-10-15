<?php

namespace Webkul\Pos\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Pos\Models\PosRegister;
use Webkul\Security\Models\User;

class PosRegisterPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pos_pos::register');
    }

    public function view(User $user, PosRegister $register): bool
    {
        return $user->can('view_pos_pos::register') || $user->can('view_any_pos_pos::register');
    }

    public function create(User $user): bool
    {
        return $user->can('create_pos_pos::register');
    }

    public function update(User $user, PosRegister $register): bool
    {
        return $user->can('update_pos_pos::register');
    }

    public function delete(User $user, PosRegister $register): bool
    {
        return $user->can('delete_pos_pos::register');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pos_pos::register');
    }
}
