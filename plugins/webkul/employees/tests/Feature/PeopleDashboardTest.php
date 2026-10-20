<?php

use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Filament\Widgets\ExpiringContractsWidget;
use Webkul\Employee\Filament\Widgets\PeopleAttentionWidget;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Services\RolePermissionSynchronizer;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('lists employees with expiring contracts and clickable overview links', function (): void {
    $employee = Employee::factory()->create([
        'name'              => 'Expiring Contract Employee',
        'is_active'         => true,
        'user_id'           => null,
        'contract_end_date' => now()->addDays(10)->toDateString(),
    ]);
    Employee::factory()->create([
        'name'              => 'Far Contract Employee',
        'is_active'         => true,
        'user_id'           => null,
        'contract_end_date' => now()->addYear()->toDateString(),
    ]);

    $user = FilamentHelper::actingAs([
        'widget_employee_expiring_contracts_widget',
        'view_employee_employee',
        'view_any_employee_employee',
    ]);
    $hrdRole = Role::query()->firstOrCreate(['name' => 'HRD', 'guard_name' => 'web']);
    $user->syncRoles([$hrdRole]);

    Livewire::test(ExpiringContractsWidget::class)
        ->assertOk()
        ->assertSee('Expiring Contract Employee')
        ->assertSeeHtml('/admin/employees/employees/'.$employee->getRouteKey().'/overview')
        ->assertDontSee('Far Contract Employee');
});

it('lists active employees without a related user in the attention widget', function (): void {
    Employee::factory()->create([
        'name'      => 'No Login Employee',
        'is_active' => true,
        'user_id'   => null,
    ]);

    $user = FilamentHelper::actingAs([
        'widget_employee_people_attention_widget',
        'view_employee_employee',
        'view_any_employee_employee',
    ]);
    $hrdRole = Role::query()->firstOrCreate(['name' => 'HRD', 'guard_name' => 'web']);
    $user->syncRoles([$hrdRole]);

    Livewire::test(PeopleAttentionWidget::class)
        ->assertOk()
        ->assertSee('No Login Employee');
});

it('grants people dashboard permissions to admin, hrd and operation manager only', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $permissions = [
        'page_employee_people_dashboard',
        'widget_employee_people_stats_widget',
        'widget_employee_expiring_contracts_widget',
        'widget_employee_people_attention_widget',
    ];

    foreach ($permissions as $permission) {
        expect(Permission::query()->where('name', $permission)->exists())->toBeTrue();

        foreach (['Admin', 'HRD', 'Operation Manager'] as $roleName) {
            $role = Role::query()->where('name', $roleName)->firstOrFail();
            expect($role->hasPermissionTo($permission))->toBeTrue();
        }

        $staffRole = Role::query()->where('name', 'Staff')->firstOrFail();
        expect($staffRole->hasPermissionTo($permission))->toBeFalse();
    }
});
