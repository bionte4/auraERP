<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Filament\Resources\EmployeeResource;
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

it('scopes the employee resource query for operation managers', function (): void {
    $omUser = FilamentHelper::actingAs([
        'view_employee_employee',
        'view_any_employee_employee',
        'create_employee_employee',
        'update_employee_employee',
        'delete_employee_employee',
    ]);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $omUser->syncRoles([$omRole]);

    $ownEmployee = Employee::factory()->create([
        'name'    => 'OM Self',
        'user_id' => $omUser->id,
    ]);

    $directReport = Employee::factory()->create([
        'name'      => 'Direct Report',
        'user_id'   => null,
        'parent_id' => $ownEmployee->id,
    ]);

    $createdByOm = Employee::factory()->create([
        'name'       => 'Created By OM',
        'user_id'    => null,
        'creator_id' => $omUser->id,
    ]);

    $unrelated = Employee::factory()->create([
        'name'    => 'Unrelated Employee',
        'user_id' => null,
    ]);


    $visibleIds = EmployeeResource::getEloquentQuery()->pluck('id')->all();

    expect($visibleIds)
        ->toContain($ownEmployee->id, $directReport->id, $createdByOm->id)
        ->not->toContain($unrelated->id);
});

it('does not scope the employee resource query for hrd', function (): void {
    $hrdUser = FilamentHelper::actingAs([
        'view_employee_employee',
        'view_any_employee_employee',
    ]);
    $hrdRole = Role::query()->firstOrCreate(['name' => 'HRD', 'guard_name' => 'web']);
    $hrdUser->syncRoles([$hrdRole]);

    $employee = Employee::factory()->create([
        'name'    => 'Any Employee',
        'user_id' => null,
    ]);


    expect(EmployeeResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($employee->id);
});

it('grants operation manager employee create, update and delete permissions', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $omRole = Role::query()->where('name', 'Operation Manager')->firstOrFail();

    foreach ([
        'create_employee_employee',
        'update_employee_employee',
        'delete_employee_employee',
    ] as $permission) {
        expect(Permission::query()->where('name', $permission)->exists())->toBeTrue();
        expect($omRole->hasPermissionTo($permission))->toBeTrue();
    }

    // Deletion is soft-delete per record only: no bulk or permanent delete.
    foreach ([
        'delete_any_employee_employee',
        'force_delete_employee_employee',
        'force_delete_any_employee_employee',
    ] as $permission) {
        expect($omRole->hasPermissionTo($permission))->toBeFalse();
    }

    $staffRole = Role::query()->where('name', 'Staff')->firstOrFail();

    foreach (['create_employee_employee', 'update_employee_employee', 'delete_employee_employee'] as $permission) {
        expect($staffRole->hasPermissionTo($permission))->toBeFalse();
    }
});
