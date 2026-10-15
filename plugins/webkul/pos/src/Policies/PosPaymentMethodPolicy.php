<?php

namespace Webkul\Pos\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Pos\Models\PosPaymentMethod;
use Webkul\Security\Models\User;

class PosPaymentMethodPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_pos_pos::payment::method');
    }

    public function view(User $user, PosPaymentMethod $method): bool
    {
        return $user->can('view_pos_pos::payment::method') || $user->can('view_any_pos_pos::payment::method');
    }

    public function create(User $user): bool
    {
        return $user->can('create_pos_pos::payment::method');
    }

    public function update(User $user, PosPaymentMethod $method): bool
    {
        return $user->can('update_pos_pos::payment::method');
    }

    public function delete(User $user, PosPaymentMethod $method): bool
    {
        return $user->can('delete_pos_pos::payment::method');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_pos_pos::payment::method');
    }
}
