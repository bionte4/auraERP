<?php

use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectEmployeeAssigner;
use Webkul\Project\Services\WeeklyOperationsReportData;
use Webkul\Security\Models\User;
use Webkul\Security\Support\AdminAccess;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function makeRoleUser(string $roleName): User
{
    $user = User::withoutEvents(fn () => User::factory()->create([
        'password'  => Hash::make('password'),
        'is_active' => true,
    ]));

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate([
            'name'       => $roleName,
            'guard_name' => $guard,
        ]);
    }

    $user->assignRole($roleName);

    return $user;
}

it('scopes active projects for operation managers', function (): void {
    $om = makeRoleUser('Operation Manager');

    $mine = Project::factory()->create(['is_active' => true, 'user_id' => $om->id]);
    Project::factory()->create(['is_active' => true]);

    $report = app(WeeklyOperationsReportData::class)->build($om);
    $active = collect($report['metrics'])->firstWhere('key', 'active_projects');

    expect($report['is_unscoped'])->toBeFalse()
        ->and($active['value'])->toBe(1);
});

it('keeps company-wide scope for hrd', function (): void {
    $hrd = makeRoleUser('HRD');

    Project::factory()->count(2)->create(['is_active' => true]);

    $report = app(WeeklyOperationsReportData::class)->build($hrd);
    $active = collect($report['metrics'])->firstWhere('key', 'active_projects');

    expect($report['is_unscoped'])->toBeTrue()
        ->and($active['value'])->toBeGreaterThanOrEqual(2);
});

it('limits workforce load to scoped assigned employees for om', function (): void {
    $om = makeRoleUser('Operation Manager');

    $mine = Project::factory()->create(['is_active' => true, 'user_id' => $om->id]);
    $other = Project::factory()->create(['is_active' => true]);

    $inScope = Employee::create(['name' => 'In Scope Worker', 'is_active' => true]);
    $outScope = Employee::create(['name' => 'Out Scope Worker', 'is_active' => true]);

    app(ProjectEmployeeAssigner::class)->sync($mine, [$inScope->id]);
    app(ProjectEmployeeAssigner::class)->sync($other, [$outScope->id]);

    $rows = app(WeeklyOperationsReportData::class)->loadRows(50, $om);
    $names = collect($rows)->pluck('name');

    expect($names)->toContain('In Scope Worker')
        ->and($names)->not->toContain('Out Scope Worker');
});

it('allows only admin hrd and operation manager viewers', function (): void {
    $service = app(WeeklyOperationsReportData::class);

    $om = makeRoleUser('Operation Manager');
    $hrd = makeRoleUser('HRD');
    $staff = makeRoleUser('Employee');

    expect($service->viewerMayAccess($om))->toBeTrue()
        ->and($service->viewerMayAccess($hrd))->toBeTrue()
        ->and($service->viewerMayAccess($staff))->toBeFalse();

    if (class_exists(AdminAccess::class)) {
        $admin = makeRoleUser(config('filament-shield.panel_user.name', 'Admin') ?: 'Admin');
        expect($service->viewerMayAccess($admin))->toBeTrue();
    }
});
