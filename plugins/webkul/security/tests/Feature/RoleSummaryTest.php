<?php

use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Security\Filament\Pages\RoleSummary;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('renders the role summary with cards and matrix for admin', function (): void {
    FilamentHelper::bootAdminPanel();

    $user = User::query()->firstOrCreate(
        ['email' => 'role-summary-admin@example.com'],
        ['name' => 'Role Summary Admin', 'password' => bcrypt('password')]
    );

    $adminRole = Role::query()->firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    $adminRole->syncPermissions(Permission::query()->get());
    $user->syncRoles([$adminRole]);

    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $viewAny = Permission::query()->firstOrCreate(['name' => 'view_any_employee_employee', 'guard_name' => 'web']);
    $omRole->syncPermissions([$viewAny]);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    Livewire::test(RoleSummary::class)
        ->assertOk()
        ->assertSee('Operation Manager')
        ->assertSee('Employee')
        ->assertSee('view_any_employee_employee');
});

it('denies the role summary to non-admin users', function (): void {
    $user = User::factory()->create();
    $staffRole = Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
    $user->syncRoles([$staffRole]);

    $this->actingAs($user);

    expect(RoleSummary::canAccess())->toBeFalse()
        ->and(RoleSummary::shouldRegisterNavigation())->toBeFalse();
});
