<?php

use Livewire\Livewire;
use Spatie\Permission\Models\Role;
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
