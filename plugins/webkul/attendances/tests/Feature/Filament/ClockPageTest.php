<?php

require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';

use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Attendance\Filament\Pages\ClockPage;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\AttendanceSite;
use Webkul\Attendance\Models\AttendanceTrack;
use Webkul\Employee\Models\Employee;

require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    if (! Schema::hasTable('attendances_sites')) {
        Artisan::call('migrate', [
            '--force'          => true,
            '--no-interaction' => true,
            '--path'           => 'plugins/webkul/attendances/database/migrations/2026_10_14_160000_create_attendances_sites_table.php',
        ]);
    }

    URL::resolveMissingNamedRoutesUsing(fn () => '/');

    Filament::setCurrentPanel(Filament::getPanel('admin'));

});

it('records a phone clock-in with a matching face and coordinates', function () {
    $user = FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'create_attendance_attendance',
        'update_attendance_attendance',
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'       => 'Uji Absensi Ponsel',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $employee->forceFill(['user_id' => $user->id])->save();

    $descriptor = array_fill(0, 128, 0.1);

    Carbon::setTestNow(Carbon::parse('1999-05-02 08:05:00'));

    try {
        Livewire::test(ClockPage::class)
            ->set('requiresFace', true)
            ->assertOk()
            ->assertSee(__('attendances::filament/pages/clock.actions.enroll'))
            ->call('enroll', $descriptor)
            ->assertSee(__('attendances::filament/pages/clock.actions.clock-in'))
            ->call('clockIn', -6.17511, 106.82715, 14, $descriptor)
            ->assertOk()
            ->call('clockOut', -6.1754, 106.8275, 11, $descriptor);
    } finally {
        Carbon::setTestNow();
    }

    $attendance = Attendance::withoutGlobalScopes()
        ->where('employee_id', $employee->id)
        ->whereDate('work_date', '1999-05-02')
        ->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->check_in_accuracy)->toBe(14)
        ->and($attendance->check_out_accuracy)->toBe(11)
        ->and($attendance->checkInMapUrl())->toContain('openstreetmap.org');
});

it('does not clock in when the location is missing', function () {
    $user = FilamentHelper::actingAs([
        'create_attendance_attendance',
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'       => 'Uji Tanpa Lokasi',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $employee->forceFill(['user_id' => $user->id])->save();

    $descriptor = array_fill(0, 128, 0.1);

    Livewire::test(ClockPage::class)
        ->set('requiresFace', true)
        ->call('enroll', $descriptor)
        ->call('clockIn', null, null, null, $descriptor);

    expect(Attendance::withoutGlobalScopes()->where('employee_id', $employee->id)->exists())->toBeFalse();
});

it('does not clock in when the face does not match', function () {
    $user = FilamentHelper::actingAs([
        'create_attendance_attendance',
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'       => 'Uji Wajah Berbeda',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $employee->forceFill(['user_id' => $user->id])->save();

    Livewire::test(ClockPage::class)
        ->set('requiresFace', true)
        ->call('enroll', array_fill(0, 128, 0.1))
        ->call('clockIn', -6.17511, 106.82715, 14, array_fill(0, 128, 0.9));

    expect(Attendance::withoutGlobalScopes()->where('employee_id', $employee->id)->exists())->toBeFalse();
});

it('records a gps trail while the shift is open', function () {
    $user = FilamentHelper::actingAs([
        'create_attendance_attendance',
        'update_attendance_attendance',
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'       => 'Uji Jejak GPS',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $employee->forceFill(['user_id' => $user->id])->save();

    $descriptor = array_fill(0, 128, 0.2);

    Carbon::setTestNow(Carbon::parse('1999-07-03 08:10:00'));

    try {
        $component = Livewire::test(ClockPage::class)
            ->set('requiresFace', true)
            ->call('enroll', $descriptor)
            ->call('clockIn', -6.2, 106.8, 10, $descriptor);

        $attendance = Attendance::withoutGlobalScopes()
            ->where('employee_id', $employee->id)
            ->first();

        expect(AttendanceTrack::withoutGlobalScopes()->where('attendance_id', $attendance->id)->count())->toBe(1);

        Carbon::setTestNow(Carbon::parse('1999-07-03 08:10:40'));

        $component->call('track', -6.201, 106.801, 9);

        expect(AttendanceTrack::withoutGlobalScopes()->where('attendance_id', $attendance->id)->count())->toBe(2);

        $component->call('clockOut', -6.202, 106.802, 7, $descriptor);

        expect(AttendanceTrack::withoutGlobalScopes()->where('attendance_id', $attendance->id)->count())->toBe(3);

        $component->call('track', -6.203, 106.803, 6);

        expect(AttendanceTrack::withoutGlobalScopes()->where('attendance_id', $attendance->id)->count())->toBe(3);
    } finally {
        Carbon::setTestNow();
    }
});

it('clocks in from a laptop without a face', function () {
    $user = FilamentHelper::actingAs([
        'create_attendance_attendance',
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'       => 'Uji Laptop',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $employee->forceFill(['user_id' => $user->id])->save();

    Livewire::test(ClockPage::class)
        ->assertDontSee(__('attendances::filament/pages/clock.actions.enroll'))
        ->call('clockIn', -6.2, 106.8, 30, null)
        ->assertOk();

    expect(Attendance::withoutGlobalScopes()->where('employee_id', $employee->id)->exists())->toBeTrue();
});

it('rejects a clock-in outside every active work site', function () {
    $user = FilamentHelper::actingAs([
        'create_attendance_attendance',
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'       => 'Uji Di Luar Site',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $employee->forceFill(['user_id' => $user->id])->save();

    AttendanceSite::query()->create([
        'company_id'    => $user->default_company_id,
        'name'          => 'Proyek Menteng',
        'latitude'      => -6.2,
        'longitude'     => 106.8,
        'radius_meters' => 150,
        'is_active'     => true,
    ]);

    Livewire::test(ClockPage::class)
        ->call('clockIn', -6.3, 106.9, 10, null);

    expect(Attendance::withoutGlobalScopes()->where('employee_id', $employee->id)->exists())->toBeFalse();
});

it('stores the matching work site when the clock-in is inside its radius', function () {
    $user = FilamentHelper::actingAs([
        'create_attendance_attendance',
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'       => 'Uji Di Dalam Site',
        'is_active'  => true,
        'company_id' => $user->default_company_id,
    ]);

    $employee->forceFill(['user_id' => $user->id])->save();

    $site = AttendanceSite::query()->create([
        'company_id'    => $user->default_company_id,
        'name'          => 'Proyek Menteng',
        'latitude'      => -6.2,
        'longitude'     => 106.8,
        'radius_meters' => 500,
        'is_active'     => true,
    ]);

    Livewire::test(ClockPage::class)
        ->call('clockIn', -6.2004, 106.8004, 12, null);

    $attendance = Attendance::withoutGlobalScopes()->where('employee_id', $employee->id)->first();

    expect($attendance)->not->toBeNull()
        ->and($attendance->check_in_site_id)->toBe($site->id);
});
