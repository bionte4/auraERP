<?php

use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource\Pages\EditUser;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('saves assigned teams without listing every project team on the user form', function (): void {
    FilamentHelper::actingAs([
        'view_any_security_user',
        'view_security_user',
        'update_security_user',
    ]);

    $assigned = Team::query()->create(['name' => 'Assigned Alpha Team']);
    $other = Team::query()->create(['name' => 'Unassigned Omega Project']);
    $company = Company::query()->first();
    $staffRole = Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);

    $user = User::withoutEvents(fn () => User::factory()->create([
        'default_company_id'  => $company?->id,
        'resource_permission' => PermissionType::GROUP,
    ]));
    $user->assignRole($staffRole);
    $user->allowedCompanies()->sync(array_filter([$company?->id]));
    $user->teams()->sync([$assigned->id]);

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->assertOk()
        ->fillForm([
            'name'                => $user->name,
            'email'               => $user->email,
            'teams'               => [$assigned->id],
            'roles'               => [$staffRole->id],
            'resource_permission' => PermissionType::GROUP->value,
            'default_company_id'  => $company?->id,
            'allowed_companies'   => array_filter([$company?->id]),
            'is_active'           => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors();
});

it('saves an existing user who already has a partner without inserting a nameless contact', function (): void {
    FilamentHelper::actingAs([
        'view_any_security_user',
        'view_security_user',
        'update_security_user',
    ]);

    $company = Company::query()->first();
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $team = Team::query()->create(['name' => 'Manage Wifi IOH']);

    $user = User::withoutEvents(fn () => User::factory()->create([
        'name'                => 'Beny Subendy',
        'email'               => 'beny.subendy.save@example.com',
        'default_company_id'  => $company?->id,
        'resource_permission' => PermissionType::GROUP,
    ]));

    $partner = Partner::factory()->create([
        'name'    => 'Beny Subendy',
        'email'   => 'beny.subendy.save@example.com',
        'user_id' => $user->id,
    ]);

    $user->forceFill(['partner_id' => $partner->id])->saveQuietly();
    $user->assignRole($omRole);
    $user->allowedCompanies()->sync(array_filter([$company?->id]));
    $user->teams()->sync([$team->id]);

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->assertOk()
        ->fillForm([
            'name'                => 'Beny Subendy',
            'email'               => 'beny.subendy.save@example.com',
            'teams'               => [$team->id],
            'roles'               => [$omRole->id],
            'resource_permission' => PermissionType::GROUP->value,
            'default_company_id'  => $company?->id,
            'allowed_companies'   => array_filter([$company?->id]),
            'is_active'           => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified(__('security::filament/resources/user/pages/edit-user.notification.title'));

    $user->refresh();

    expect($user->partner_id)->toBe($partner->id)
        ->and($partner->fresh()->name)->toBe('Beny Subendy');
});

it('saves a user whose partner contact was soft deleted', function (): void {
    FilamentHelper::actingAs([
        'view_any_security_user',
        'view_security_user',
        'update_security_user',
    ]);

    $company = Company::query()->first();
    $staffRole = Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
    $team = Team::query()->create(['name' => 'Soft Deleted Partner Team']);

    $user = User::withoutEvents(fn () => User::factory()->create([
        'name'                => 'Soft Partner User',
        'email'               => 'soft.partner.user@example.com',
        'default_company_id'  => $company?->id,
        'resource_permission' => PermissionType::GROUP,
    ]));

    $partner = Partner::factory()->create([
        'name'    => 'Soft Partner User',
        'email'   => 'soft.partner.user@example.com',
        'user_id' => $user->id,
    ]);

    $user->forceFill(['partner_id' => $partner->id])->saveQuietly();
    $user->assignRole($staffRole);
    $user->allowedCompanies()->sync(array_filter([$company?->id]));
    $partner->delete();

    Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->assertOk()
        ->fillForm([
            'name'                => 'Soft Partner User',
            'email'               => 'soft.partner.user@example.com',
            'teams'               => [$team->id],
            'roles'               => [$staffRole->id],
            'resource_permission' => PermissionType::GROUP->value,
            'default_company_id'  => $company?->id,
            'allowed_companies'   => array_filter([$company?->id]),
            'is_active'           => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified(__('security::filament/resources/user/pages/edit-user.notification.title'));

    expect($user->fresh()->partner_id)->not->toBeNull()
        ->and(Partner::withoutGlobalScopes()->find($user->fresh()->partner_id)?->name)->toBe('Soft Partner User');
});
