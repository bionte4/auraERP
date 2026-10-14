<?php

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Attendance\Enums\AttendanceStatus;
use Webkul\Attendance\Filament\Resources\AttendanceResource;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Pages\ManageAttendances;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;

require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    URL::resolveMissingNamedRoutesUsing(fn () => '/');

    $panel = Filament::getPanel('admin');

    Route::name('filament.'.$panel->getId().'.')->group(function () use ($panel): void {
        AttendanceResource::registerRoutes($panel);
    });
});

it('shows the attendance page and records clock-in for the linked employee', function () {
    $user = FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'create_attendance_attendance',
        'update_attendance_attendance',
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'       => 'Uji Absensi',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $employee->forceFill(['user_id' => $user->id])->save();

    Carbon::setTestNow(Carbon::parse('1999-03-01 08:15:00'));

    try {
        Livewire::test(ManageAttendances::class)
            ->assertOk()
            ->callAction('clockIn');
    } finally {
        Carbon::setTestNow();
    }

    $attendance = Attendance::withoutGlobalScopes()
        ->where('employee_id', $employee->id)
        ->whereDate('work_date', '1999-03-01')
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->status)->toBe(AttendanceStatus::Present);
});
