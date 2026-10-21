<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Security\Filament\Resources\RoleResource;
use Webkul\Security\Services\RolePermissionSynchronizer;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('splits accounting staff from accounting manager delete rights', function (): void {
    foreach ([
        'view_any_account_invoice',
        'view_account_invoice',
        'create_account_invoice',
        'update_account_invoice',
        'delete_account_invoice',
        'delete_any_account_invoice',
        'delete_account_payment',
        'force_delete_account_invoice',
        'page_project_project_budget_control',
        'page_support_my_day_dashboard',
        'page_project_dashboard',
    ] as $name) {
        Permission::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']);
    }

    app(RolePermissionSynchronizer::class)->syncAll();

    $staff = Role::query()->where('name', 'Accounting')->where('guard_name', 'web')->first();
    $manager = Role::query()->where('name', 'Accounting Manager')->where('guard_name', 'web')->first();

    expect($staff)->not->toBeNull()
        ->and($manager)->not->toBeNull();

    $staffPermissions = $staff->permissions->pluck('name')->all();
    $managerPermissions = $manager->permissions->pluck('name')->all();

    expect($staffPermissions)
        ->toContain('create_account_invoice', 'view_any_account_invoice', 'page_project_project_budget_control')
        ->not->toContain('delete_account_invoice', 'delete_any_account_invoice', 'delete_account_payment', 'force_delete_account_invoice');

    expect($managerPermissions)
        ->toContain('delete_account_invoice', 'delete_any_account_invoice', 'page_project_project_budget_control');
});

it('protects the accounting manager role from deletion as a baseline role', function (): void {
    $manager = \Webkul\Security\Models\Role::query()->firstOrCreate(['name' => 'Accounting Manager', 'guard_name' => 'web']);

    expect(RoleResource::isBaselineRoleRecord($manager))->toBeTrue();
});
