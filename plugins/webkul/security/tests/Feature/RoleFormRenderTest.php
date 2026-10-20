<?php

use Filament\Facades\Filament;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Security\Filament\Resources\RoleResource\Pages\CreateRole;
use Webkul\Security\Filament\Resources\RoleResource\Pages\EditRole;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('renders permission sections on create and edit role pages', function (): void {
    FilamentHelper::bootAdminPanel();

    $user = \Webkul\Security\Models\User::query()->firstOrCreate(
        ['email' => 'role-form-admin@example.com'],
        ['name' => 'Role Form Admin', 'password' => bcrypt('password')]
    );

    $adminRole = Role::query()->firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
    $adminRole->syncPermissions(Permission::query()->get());
    $user->syncRoles([$adminRole]);

    $this->actingAs($user);

    Filament::setCurrentPanel(Filament::getPanel('admin'));

    $createHtml = Livewire::test(CreateRole::class)->html();

    expect($createHtml)
        ->toContain('Resources')
        ->toContain('Pages')
        ->toContain('Widgets')
        ->toContain('checkbox');

    $hrdRole = Role::query()->firstOrCreate(['name' => 'HRD', 'guard_name' => 'web']);

    $editHtml = Livewire::test(EditRole::class, ['record' => $hrdRole->id])->html();

    expect($editHtml)
        ->toContain('Resources')
        ->toContain('checkbox');
});
