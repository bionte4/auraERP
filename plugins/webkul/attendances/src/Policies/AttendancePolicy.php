<?php

namespace Webkul\Attendance\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Webkul\Attendance\Models\Attendance;
use Webkul\Security\Models\User;
use Webkul\Security\Traits\HasScopedPermissions;

class AttendancePolicy
{
    use HandlesAuthorization, HasScopedPermissions;

    public function viewAny(User $user): bool
    {
        return $user->can('view_any_attendance_attendance')
            || $user->can('page_attendance_clock_page');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        if (! $user->can('view_any_attendance_attendance') && ! $user->can('view_attendance_attendance')) {
            return false;
        }

        return $this->hasAccess($user, $attendance, 'employee');
    }

    public function create(User $user): bool
    {
        return $user->can('create_attendance_attendance')
            || $user->can('page_attendance_clock_page');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        if (! $user->can('update_attendance_attendance')) {
            return false;
        }

        return $this->hasAccess($user, $attendance, 'employee');
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        if (! $user->can('delete_attendance_attendance')) {
            return false;
        }

        return $this->hasAccess($user, $attendance, 'employee');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_attendance_attendance');
    }
}
