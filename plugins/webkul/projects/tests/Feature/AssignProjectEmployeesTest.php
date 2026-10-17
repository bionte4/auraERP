<?php

use Livewire\Livewire;
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
require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';

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

it('assigns projects to an employee from the employee side', function (): void {
    $user = User::withoutEvents(fn () => User::factory()->create());
    $partner = Partner::factory()->create();
    $user->forceFill(['partner_id' => $partner->id])->save();

    $employee = Employee::create([
        'name'       => 'Employee With Projects',
        'user_id'    => $user->id,
        'partner_id' => $partner->id,
        'is_active'  => true,
    ]);

    $projectA = Project::factory()->create(['name' => 'Project Alpha']);
    $projectB = Project::factory()->create(['name' => 'Project Beta']);

    $result = app(ProjectEmployeeAssigner::class)->syncProjectsForEmployee(
        $employee,
        [$projectA->id, $projectB->id],
        $user->id,
    );

    expect($result['assigned'])->toBe(2)
        ->and($employee->projects()->pluck('projects_projects.id')->all())
        ->toContain($projectA->id, $projectB->id)
        ->and($projectA->fresh()->isFollowedBy($partner))->toBeTrue()
        ->and($projectB->fresh()->isFollowedBy($partner))->toBeTrue();
});

it('saves assignment from the assign employees page form', function (): void {
    FilamentHelper::actingAs([
        'page_project_assign_project_employees',
    ]);

    $employee = Employee::create([
        'name'      => 'Form Assigned Employee',
        'is_active' => true,
    ]);

    $project = Project::factory()->create(['name' => 'Form Project']);

    Livewire::test(AssignProjectEmployees::class)
        ->assertOk()
        ->assertFormFieldExists('project_id')
        ->assertFormFieldExists('employee_ids')
        ->fillForm([
            'project_id'   => $project->id,
            'employee_ids' => [(string) $employee->id],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($project->employees()->pluck('employees_employees.id')->all())->toContain($employee->id);
});

it('preselects employees already assigned when a project is chosen', function (): void {
    FilamentHelper::actingAs([
        'page_project_assign_project_employees',
    ]);

    $employee = Employee::create([
        'name'      => 'Already On Project',
        'is_active' => true,
    ]);

    $project = Project::factory()->create(['name' => 'Existing Project']);
    app(ProjectEmployeeAssigner::class)->sync($project, [$employee->id]);

    Livewire::test(AssignProjectEmployees::class)
        ->assertOk()
        ->set('data.project_id', $project->id)
        ->assertSet('data.employee_ids', [(string) $employee->id]);
});

it('grants assign employees page permission to admin and operation manager only', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $permission = 'page_project_assign_project_employees';

    expect(Permission::query()->where('name', $permission)->exists())->toBeTrue();

    foreach (['Admin', 'Operation Manager'] as $roleName) {
        $role = Role::query()->where('name', $roleName)->firstOrFail();
        expect($role->hasPermissionTo($permission))->toBeTrue();
    }

    $staffRole = Role::query()->where('name', 'Staff')->firstOrFail();
    expect($staffRole->hasPermissionTo($permission))->toBeFalse();
});
