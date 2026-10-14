<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Webkul\Attendance\Data\AttendanceLocation;
use Webkul\Attendance\Enums\ClockResult;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\AttendanceTrack;
use Webkul\Attendance\Services\AttendanceClock;
use Webkul\Attendance\Services\AttendanceTracker;
use Webkul\Employee\Models\Employee;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    if (! Schema::hasTable('attendances_sites')) {
        Artisan::call('migrate', [
            '--force'          => true,
            '--no-interaction' => true,
            '--path'           => 'plugins/webkul/attendances/database/migrations/2026_10_14_160000_create_attendances_sites_table.php',
        ]);
    }
});

it('keeps a location trail during an open shift and stops after clock-out', function () {
    $employee = Employee::withoutGlobalScopes()->create([
        'name'      => 'Uji Jejak Layanan',
        'is_active' => true,
    ]);

    $start = Carbon::parse('1999-06-01 08:00:00');
    Carbon::setTestNow($start);

    $clock = app(AttendanceClock::class);
    $tracker = app(AttendanceTracker::class);
    $location = AttendanceLocation::tryMake(-6.2, 106.8, 12);

    try {
        expect($clock->clockIn($employee, $start, $location))->toBe(ClockResult::Recorded);

        $attendance = Attendance::withoutGlobalScopes()->where('employee_id', $employee->id)->first();

        expect($tracker->append($attendance, $location))->toBeFalse()
            ->and(AttendanceTrack::withoutGlobalScopes()->where('attendance_id', $attendance->id)->count())->toBe(1);

        Carbon::setTestNow($start->copy()->addSeconds(31));

        expect($tracker->append($attendance->fresh(), AttendanceLocation::tryMake(-6.21, 106.81, 8)))->toBeTrue()
            ->and(AttendanceTrack::withoutGlobalScopes()->where('attendance_id', $attendance->id)->count())->toBe(2);

        expect($clock->clockOut($employee, now(), AttendanceLocation::tryMake(-6.22, 106.82, 6)))->toBe(ClockResult::Recorded)
            ->and($tracker->append($attendance->fresh(), $location))->toBeFalse()
            ->and(AttendanceTrack::withoutGlobalScopes()->where('attendance_id', $attendance->id)->count())->toBe(3);
    } finally {
        Carbon::setTestNow();
    }
});
