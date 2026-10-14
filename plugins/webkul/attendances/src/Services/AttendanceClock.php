<?php

namespace Webkul\Attendance\Services;

use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Webkul\Attendance\Data\AttendanceLocation;
use Webkul\Attendance\Enums\AttendanceStatus;
use Webkul\Attendance\Enums\ClockResult;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;

class AttendanceClock
{
    public const LATE_HOUR = 9;

    public const LATE_MINUTE = 0;

    public function employeeForUser(int $userId): ?Employee
    {
        return Employee::query()->where('user_id', $userId)->first();
    }

    public function statusForArrival(Carbon $at): AttendanceStatus
    {
        $cutoff = $at->copy()->setTime(self::LATE_HOUR, self::LATE_MINUTE);

        return $at->greaterThan($cutoff)
            ? AttendanceStatus::Late
            : AttendanceStatus::Present;
    }

    public function clockIn(Employee $employee, ?Carbon $at = null, ?AttendanceLocation $location = null, ?int $siteId = null): ClockResult
    {
        $at ??= now();

        $attendance = $this->today($employee, $at);

        if ($attendance?->check_in !== null) {
            return ClockResult::AlreadyClockedIn;
        }

        $attendance ??= new Attendance([
            'employee_id' => $employee->id,
            'work_date'   => $at->toDateString(),
        ]);

        $attendance->company_id = $employee->company_id;
        $attendance->check_in = $at;
        $attendance->status = $this->statusForArrival($at);
        $this->applyLocation($attendance, 'check_in', $location, $siteId);

        try {
            $attendance->save();
        } catch (UniqueConstraintViolationException) {
            return ClockResult::AlreadyClockedIn;
        }

        $this->rememberTrack($attendance, $location, $at);

        return ClockResult::Recorded;
    }

    public function clockOut(Employee $employee, ?Carbon $at = null, ?AttendanceLocation $location = null, ?int $siteId = null): ClockResult
    {
        $at ??= now();

        $attendance = $this->today($employee, $at);

        if ($attendance?->check_in === null || $attendance->check_out !== null) {
            return ClockResult::MissingClockIn;
        }

        $this->rememberTrack($attendance, $location, $at);
        $attendance->check_out = $at;
        $this->applyLocation($attendance, 'check_out', $location, $siteId);
        $attendance->save();

        return ClockResult::Recorded;
    }

    protected function rememberTrack(Attendance $attendance, ?AttendanceLocation $location, Carbon $at): void
    {
        if ($location === null) {
            return;
        }

        app(AttendanceTracker::class)->append($attendance, $location, $at, force: true);
    }

    protected function applyLocation(Attendance $attendance, string $prefix, ?AttendanceLocation $location, ?int $siteId = null): void
    {
        if ($location === null) {
            return;
        }

        $attendance->{$prefix.'_latitude'} = $location->latitude;
        $attendance->{$prefix.'_longitude'} = $location->longitude;
        $attendance->{$prefix.'_accuracy'} = $location->accuracyMeters;
        $attendance->{$prefix.'_site_id'} = $siteId;
    }

    protected function today(Employee $employee, Carbon $at): ?Attendance
    {
        return Attendance::query()
            ->where('employee_id', $employee->id)
            ->whereDate('work_date', $at->toDateString())
            ->first();
    }
}
