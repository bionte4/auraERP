<?php

use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Services\RolePermissionSynchronizer;
use Webkul\Support\Filament\Pages\TeamBoard;

require_once __DIR__.'/../Helpers/FilamentHelper.php';
require_once __DIR__.'/../Helpers/SecurityHelper.php';
require_once __DIR__.'/../Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('grants operation managers team view and create/update but not delete', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $omRole = Role::query()->where('name', 'Operation Manager')->where('guard_name', 'web')->firstOrFail();

    foreach (['view_any_security_team', 'view_security_team', 'create_security_team', 'update_security_team'] as $permission) {
        expect($omRole->hasPermissionTo($permission))->toBeTrue($permission);
    }

    foreach (['delete_security_team', 'delete_any_security_team'] as $permission) {
        expect($omRole->hasPermissionTo($permission))->toBeFalse($permission);
    }

    $staffRole = Role::query()->where('name', 'Staff')->where('guard_name', 'web')->firstOrFail();

    expect($staffRole->hasPermissionTo('create_security_team'))->toBeFalse();
});

it('allows operation managers, team leaders and staff to open the team board', function (): void {
    foreach (['Operation Manager', 'Team Leader', 'Staff'] as $roleName) {
        $user = User::withoutEvents(fn () => User::factory()->create());
        $role = Role::query()->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
        $user->syncRoles([$role]);

        $this->actingAs($user);

        expect(TeamBoard::canAccess())->toBeTrue($roleName);
    }

    $accounting = User::withoutEvents(fn () => User::factory()->create());
    $accountingRole = Role::query()->firstOrCreate(['name' => 'Accounting', 'guard_name' => 'web']);
    $accounting->syncRoles([$accountingRole]);

    $this->actingAs($accounting);

    expect(TeamBoard::canAccess())->toBeFalse();
});

it('renders the team board for an operation manager', function (): void {
    FilamentHelper::bootAdminPanel();

    $user = User::withoutEvents(fn () => User::factory()->create(['name' => 'OM Board User']));
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $user->syncRoles([$omRole]);

    $this->actingAs($user);
    \Filament\Facades\Filament::setCurrentPanel(\Filament\Facades\Filament::getPanel('admin'));

    Livewire::test(TeamBoard::class)->assertOk();
});
