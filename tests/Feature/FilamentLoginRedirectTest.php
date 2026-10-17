<?php

use App\Http\Responses\FilamentLoginResponse;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Security\Filament\Auth\Login;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\MyDayDashboard;
use Webkul\Support\Filament\Pages\WelcomeDashboard;

require_once __DIR__.'/../../plugins/webkul/support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../plugins/webkul/support/tests/Helpers/SecurityHelper.php';

beforeEach(function (): void {
    SecurityHelper::disableUserEvents();
    FilamentHelper::bootAdminPanel();
});

it('sends admins to welcome after login even when intended url is the login page', function (): void {
    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    Role::query()->firstOrCreate([
        'name'       => $adminRoleName,
        'guard_name' => 'web',
    ]);

    User::withoutEvents(fn () => User::factory()->create([
        'email'     => 'login-admin@example.com',
        'password'  => Hash::make('password'),
        'is_active' => true,
    ]))->syncRoles([$adminRoleName]);

    session(['url.intended' => url('/admin/login')]);

    Livewire::test(Login::class)
        ->fillForm([
            'email'    => 'login-admin@example.com',
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect(WelcomeDashboard::getUrl(panel: 'admin'));
});

it('sends non-admin users to my day after login', function (): void {
    Role::query()->firstOrCreate([
        'name'       => 'Staff',
        'guard_name' => 'web',
    ]);

    User::withoutEvents(fn () => User::factory()->create([
        'email'     => 'login-staff@example.com',
        'password'  => Hash::make('password'),
        'is_active' => true,
    ]))->syncRoles(['Staff']);

    session(['url.intended' => url('/admin/login')]);

    Livewire::test(Login::class)
        ->fillForm([
            'email'    => 'login-staff@example.com',
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertRedirect(MyDayDashboard::getUrl(panel: 'admin'));
});

it('does not redirect an authenticated user back to the login page', function (): void {
    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    Role::query()->firstOrCreate([
        'name'       => $adminRoleName,
        'guard_name' => 'web',
    ]);

    $admin = User::withoutEvents(fn () => User::factory()->create());
    $admin->syncRoles([$adminRoleName]);

    $this->actingAs($admin);
    Filament::setCurrentPanel(Filament::getPanel('admin'));

    session(['url.intended' => url('/admin/login')]);

    expect(FilamentLoginResponse::isUnsafeAuthUrl(url('/admin/login')))->toBeTrue();

    FilamentLoginResponse::forgetUnsafeIntendedUrl();

    expect(session()->has('url.intended'))->toBeFalse()
        ->and(FilamentLoginResponse::homeUrl())->toBe(WelcomeDashboard::getUrl(panel: 'admin'));

    Livewire::test(Login::class)
        ->assertRedirect(WelcomeDashboard::getUrl(panel: 'admin'));
});
