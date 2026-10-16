<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Filament\Pages\AssignProjectEmployees;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectEmployeeAssigner;
use Webkul\Security\Models\User;
use Webkul\Security\Services\RolePermissionSynchronizer;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('assigns employees to a project and syncs followers for linked partners', function (): void {
    $user = User::withoutEvents(fn () => User::factory()->create());
    $partner = Partner::factory()->create();
    $user->forceFill(['partner_id' => $partner->id])->save();

    $employee = Employee::create([
        'name'      => 'Assigned Employee',
        'user_id'   => $user->id,
        'partner_id'=> $partner->id,
        'is_active' => true,
    ]);

    $project = Project::factory()->create();

    $result = app(ProjectEmployeeAssigner::class)->sync($project, [$employee->id], $user->id);

    expect($result['assigned'])->toBe(1)
        ->and($result['without_user'])->toBe(0)
        ->and($project->employees()->pluck('employees_employees.id')->all())->toContain($employee->id)
        ->and($project->isFollowedBy($partner))->toBeTrue();
});

it('allows admin and operation manager to access the assign employees page', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $permission = 'page_project_assign_project_employees';

    expect(Permission::query()->where('name', $permission)->exists())->toBeTrue();

    foreach (['Admin', 'Operation Manager'] as $roleName) {
        $role = Role::query()->where('name', $roleName)->firstOrFail();
        expect($role->hasPermissionTo($permission))->toBeTrue();

        $user = User::withoutEvents(fn () => User::factory()->create());
        $user->syncRoles([$roleName]);
        $this->actingAs($user);

        expect(AssignProjectEmployees::canAccess())->toBeTrue();
    }
});

it('denies staff from accessing the assign employees page', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $role = Role::query()->firstOrCreate([
        'name'       => 'Staff',
        'guard_name' => 'web',
    ]);

    $user = User::withoutEvents(fn () => User::factory()->create());
    $user->syncRoles([$role->name]);
    $this->actingAs($user);

    expect(AssignProjectEmployees::canAccess())->toBeFalse();
});
