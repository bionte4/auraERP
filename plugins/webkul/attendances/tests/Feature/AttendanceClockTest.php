<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Illuminate\Support\Carbon;
use Webkul\Attendance\Data\AttendanceLocation;
use Webkul\Attendance\Enums\AttendanceStatus;
use Webkul\Attendance\Enums\ClockResult;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceClock;
use Webkul\Employee\Models\Employee;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');
});

it('marks arrivals before 09:00 as present and later arrivals as late', function () {
    $clock = app(AttendanceClock::class);

    expect($clock->statusForArrival(Carbon::parse('1999-01-01 08:30:00')))->toBe(AttendanceStatus::Present)
        ->and($clock->statusForArrival(Carbon::parse('1999-01-01 09:00:00')))->toBe(AttendanceStatus::Present)
        ->and($clock->statusForArrival(Carbon::parse('1999-01-01 09:01:00')))->toBe(AttendanceStatus::Late);
});

it('records one clock-in per employee per day and a later clock-out', function () {
    $employee = Employee::withoutGlobalScopes()->first()
        ?? Employee::withoutGlobalScopes()->create([
            'name'      => 'Uji Absensi',
            'is_active' => true,
        ]);

    expect($employee)->not->toBeNull();

    $clock = app(AttendanceClock::class);
    $arrival = Carbon::parse('1999-01-01 08:30:00');

    expect($clock->clockIn($employee, $arrival))->toBe(ClockResult::Recorded)
        ->and($clock->clockIn($employee, $arrival->copy()->addHour()))->toBe(ClockResult::AlreadyClockedIn);

    $attendance = Attendance::withoutGlobalScopes()
        ->where('employee_id', $employee->id)
        ->whereDate('work_date', '1999-01-01')
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->status)->toBe(AttendanceStatus::Present)
        ->and($attendance->company_id)->toBe($employee->company_id);

    expect($clock->clockOut($employee, Carbon::parse('1999-01-01 17:00:00')))->toBe(ClockResult::Recorded);

    $attendance->refresh();

    expect($attendance->workedLabel())->toBe('8:30');
});

it('stores the phone location on clock-in and clock-out', function () {
    $employee = Employee::withoutGlobalScopes()->first()
        ?? Employee::withoutGlobalScopes()->create([
            'name'      => 'Uji Absensi',
            'is_active' => true,
        ]);

    $clock = app(AttendanceClock::class);
    $arrival = AttendanceLocation::tryMake(-6.2, 106.816666, 18.4);
    $departure = AttendanceLocation::tryMake(-6.2004, 106.8171, 9);

    expect($arrival)->not->toBeNull()
        ->and($clock->clockIn($employee, Carbon::parse('1999-04-01 08:10:00'), $arrival))->toBe(ClockResult::Recorded)
        ->and($clock->clockOut($employee, Carbon::parse('1999-04-01 17:05:00'), $departure))->toBe(ClockResult::Recorded);

    $attendance = Attendance::withoutGlobalScopes()
        ->where('employee_id', $employee->id)
        ->whereDate('work_date', '1999-04-01')
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->check_in_latitude)->toEqual(-6.2)
        ->and($attendance->check_in_longitude)->toEqual(106.816666)
        ->and($attendance->check_in_accuracy)->toBe(18)
        ->and($attendance->check_out_accuracy)->toBe(9)
        ->and($attendance->checkInMapUrl())->toContain('mlat=-6.2');
});

it('rejects a location outside the valid coordinate range', function () {
    expect(AttendanceLocation::tryMake(91, 10))->toBeNull()
        ->and(AttendanceLocation::tryMake(-6.2, 181))->toBeNull()
        ->and(AttendanceLocation::tryMake('south', 'east'))->toBeNull();
});

it('refuses to clock out without an open clock-in', function () {
    $employee = Employee::withoutGlobalScopes()->first()
        ?? Employee::withoutGlobalScopes()->create([
            'name'      => 'Uji Absensi',
            'is_active' => true,
        ]);
    $clock = app(AttendanceClock::class);

    expect($clock->clockOut($employee, Carbon::parse('1999-02-01 17:00:00')))->toBe(ClockResult::MissingClockIn);
});
