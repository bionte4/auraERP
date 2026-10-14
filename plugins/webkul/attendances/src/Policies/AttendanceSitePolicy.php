<?php

namespace Webkul\Attendance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Attendance\Models\AttendanceSite;
use Webkul\Security\Models\User;

class AttendanceSitePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_attendance_attendance');
    }

    public function view(User $user, AttendanceSite $site): bool
    {
        return $user->can('view_any_attendance_attendance');
    }

    public function create(User $user): bool
    {
        return $user->can('create_attendance_attendance');
    }

    public function update(User $user, AttendanceSite $site): bool
    {
        return $user->can('update_attendance_attendance');
    }

    public function delete(User $user, AttendanceSite $site): bool
    {
        return $user->can('delete_attendance_attendance');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_attendance_attendance');
    }
}
