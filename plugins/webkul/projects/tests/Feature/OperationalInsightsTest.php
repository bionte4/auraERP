<?php

use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Services\EmployeeUserProvisioner;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\EmployeeCapacity;
use Webkul\Project\Services\OperationalScope;
use Webkul\Project\Services\ProjectEmployeeAssigner;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('builds capacity labels for assigned employees', function (): void {
    $employee = Employee::create([
        'name'      => 'Capacity Employee',
        'is_active' => true,
    ]);

    $project = Project::factory()->create(['is_active' => true]);
    app(ProjectEmployeeAssigner::class)->sync($project, [$employee->id]);

    $capacity = app(EmployeeCapacity::class)->forEmployees([$employee->id]);

    expect($capacity[$employee->id]['projects'])->toBe(1)
        ->and($capacity[$employee->id]['label'])->toContain('proyek');
});

it('provisions a related user from work email', function (): void {
    $email = 'new.staff.'.uniqid().'@example.com';

    $employee = Employee::create([
        'name'       => 'Provision Employee',
        'is_active'  => true,
        'work_email' => $email,
    ]);

    $result = app(EmployeeUserProvisioner::class)->provision($employee->fresh());

    expect($result['created'])->toBeTrue()
        ->and($result['password'])->not->toBeNull()
        ->and($employee->fresh()->user_id)->toBe($result['user']->id)
        ->and(Hash::check($result['password'], $result['user']->password))->toBeTrue();
});

it('scopes project ids for operation managers', function (): void {
    $om = User::withoutEvents(fn () => User::factory()->create([
        'password'  => Hash::make('password'),
        'is_active' => true,
    ]));

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate([
            'name'       => 'Operation Manager',
            'guard_name' => $guard,
        ]);
    }

    $om->assignRole('Operation Manager');

    $mine = Project::factory()->create(['is_active' => true, 'user_id' => $om->id]);
    Project::factory()->create(['is_active' => true]);

    $ids = app(OperationalScope::class)->projectIds($om);

    expect($ids)->toContain($mine->id);
});
