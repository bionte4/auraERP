<?php

use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Project\Filament\Widgets\OperationalBudgetWidget;
use Webkul\Project\Filament\Widgets\OperationManagerWorkloadWidget;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;
use Webkul\Security\Services\RolePermissionSynchronizer;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('lists operation managers with managed project counts and clickable project links', function (): void {
    $omRole = Role::query()->firstOrCreate([
        'name'       => 'Operation Manager',
        'guard_name' => 'web',
    ]);

    $operationManager = User::withoutEvents(fn () => User::factory()->create([
        'name'      => 'OM Workload User',
        'is_active' => true,
    ]));
    $operationManager->syncRoles([$omRole]);

    $staff = User::withoutEvents(fn () => User::factory()->create([
        'name'      => 'Staff Not OM',
        'is_active' => true,
    ]));
    Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
    $staff->syncRoles(['Staff']);

    $activeProject = Project::factory()->create([
        'name'      => 'Active Managed Project',
        'user_id'   => $operationManager->id,
        'is_active' => true,
    ]);
    Project::factory()->create([
        'name'      => 'Inactive Managed Project',
        'user_id'   => $operationManager->id,
        'is_active' => false,
    ]);
    Project::factory()->create([
        'name'      => 'Staff Managed Project',
        'user_id'   => $staff->id,
        'is_active' => true,
    ]);

    $admin = FilamentHelper::actingAs([
        'widget_project_operation_manager_workload_widget',
        'view_project_project',
        'view_any_project_project',
    ]);
    $adminRole = Role::query()->firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    $admin->syncRoles([$adminRole]);

    Livewire::test(OperationManagerWorkloadWidget::class)
        ->assertOk()
        ->assertSee('OM Workload User')
        ->assertSee('Active Managed Project')
        ->assertSeeHtml('/admin/project/projects/'.$activeProject->getRouteKey())
        ->assertDontSee('Staff Not OM')
        ->assertDontSee('Inactive Managed Project')
        ->assertDontSee('Staff Managed Project');

    expect($operationManager->managedProjects()->where('is_active', true)->count())->toBe(1);
});

it('grants operation manager workload widget permission to admin and operation manager only', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $permission = 'widget_project_operation_manager_workload_widget';

    expect(Permission::query()->where('name', $permission)->exists())->toBeTrue();

    foreach (['Admin', 'Operation Manager'] as $roleName) {
        $role = Role::query()->where('name', $roleName)->firstOrFail();
        expect($role->hasPermissionTo($permission))->toBeTrue();
    }

    $staffRole = Role::query()->where('name', 'Staff')->firstOrFail();
    expect($staffRole->hasPermissionTo($permission))->toBeFalse();
});

it('paginates operation managers independently from the opex budget table', function (): void {
    FilamentHelper::actingAs([
        'widget_project_operation_manager_workload_widget',
        'widget_project_operational_budget_widget',
        'view_project_project',
        'view_any_project_project',
    ]);

    $workload = Livewire::test(OperationManagerWorkloadWidget::class);
    $budget = Livewire::test(OperationalBudgetWidget::class);

    expect($workload->instance()->getTablePaginationPageName())
        ->toBe('omWorkloadPage')
        ->and($budget->instance()->getTablePaginationPageName())
        ->toBe('opexBudgetPage')
        ->and($workload->instance()->getTablePaginationPageName())
        ->not->toBe($budget->instance()->getTablePaginationPageName());
});
