<?php

namespace Webkul\Pos\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Pos\Models\PosOrder;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class PosOrderPolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pos_pos::order');
    }

    public function view(User $user, PosOrder $order): bool
    {
        if (! $user->can('view_pos_pos::order') && ! $user->can('view_any_pos_pos::order')) {
            return false;
        }

        return $this->hasAccess($user, $order, 'cashier');
    }

    public function create(User $user): bool
    {
        return $user->can('create_pos_pos::order');
    }

    public function update(User $user, PosOrder $order): bool
    {
        if (! $user->can('update_pos_pos::order')) {
            return false;
        }

        return $this->hasAccess($user, $order, 'cashier');
    }

    public function delete(User $user, PosOrder $order): bool
    {
        if (! $user->can('delete_pos_pos::order')) {
            return false;
        }

        return $this->hasAccess($user, $order, 'cashier');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pos_pos::order');
    }
}
