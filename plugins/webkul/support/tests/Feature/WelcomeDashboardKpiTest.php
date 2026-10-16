<?php

require_once __DIR__.'/../Helpers/SecurityHelper.php';

use Spatie\Permission\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\WelcomeDashboard;

beforeEach(function (): void {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('exposes total project and total employee kpi labels for admins', function () {
    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    Role::query()->firstOrCreate([
        'name'       => $adminRoleName,
        'guard_name' => 'web',
    ]);

    $admin = User::withoutEvents(fn () => User::factory()->create());
    $admin->syncRoles([$adminRoleName]);

    $this->actingAs($admin);

    expect(WelcomeDashboard::canAccess())->toBeTrue();

    $page = app(WelcomeDashboard::class);
    $titles = collect($page->getDashboardData()['kpis'] ?? [])->pluck('title')->all();

    expect($titles)->toContain('Total Project', 'Total Employee')
        ->and($titles)->not->toContain('Total Work Orders', 'Active Customers');
});

it('denies welcome dashboard access to non-admin roles', function () {
    foreach (['HRD', 'Operation Manager', 'Staff', 'Kasir'] as $roleName) {
        Role::query()->firstOrCreate([
            'name'       => $roleName,
            'guard_name' => 'web',
        ]);

        $user = User::withoutEvents(fn () => User::factory()->create());
        $user->syncRoles([$roleName]);

        $this->actingAs($user);

        expect(WelcomeDashboard::canAccess())->toBeFalse();
    }
});
