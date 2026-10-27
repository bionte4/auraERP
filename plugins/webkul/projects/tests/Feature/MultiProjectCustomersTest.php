<?php

use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Filament\Pages\MultiProjectCustomers;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\MultiProjectCustomers as MultiProjectCustomersQuery;
use Webkul\Security\Models\User;
use Webkul\Security\Services\RolePermissionSynchronizer;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('lists only customers that have two or more active projects', function (): void {
    FilamentHelper::actingAs(['page_project_multi_project_customers']);

    $multi = Partner::factory()->create(['name' => 'Multi Client Bank']);
    $single = Partner::factory()->create(['name' => 'Single Client Bank']);

    Project::factory()->create(['partner_id' => $multi->id, 'is_active' => true, 'name' => 'Kontrak A']);
    Project::factory()->create(['partner_id' => $multi->id, 'is_active' => true, 'name' => 'Kontrak B']);
    Project::factory()->create(['partner_id' => $single->id, 'is_active' => true, 'name' => 'Only One']);

    $ids = app(MultiProjectCustomersQuery::class)->partnerQuery()->pluck('id')->all();

    expect($ids)->toContain($multi->id)
        ->and($ids)->not->toContain($single->id);

    Livewire::test(MultiProjectCustomers::class)
        ->assertOk()
        ->assertSee('Multi Client Bank')
        ->assertDontSee('Single Client Bank');
});

it('scopes multi-project customers to operation manager assignments', function (): void {
    $om = FilamentHelper::actingAs(['page_project_multi_project_customers']);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $om->syncRoles([$omRole]);

    $visibleCustomer = Partner::factory()->create(['name' => 'OM Multi Client']);
    $hiddenCustomer = Partner::factory()->create(['name' => 'Other Multi Client']);

    Project::factory()->create(['partner_id' => $visibleCustomer->id, 'user_id' => $om->id, 'is_active' => true]);
    Project::factory()->create(['partner_id' => $visibleCustomer->id, 'user_id' => $om->id, 'is_active' => true]);

    $otherOm = User::withoutEvents(fn () => User::factory()->create());
    Project::factory()->create(['partner_id' => $hiddenCustomer->id, 'user_id' => $otherOm->id, 'is_active' => true]);
    Project::factory()->create(['partner_id' => $hiddenCustomer->id, 'user_id' => $otherOm->id, 'is_active' => true]);

    $ids = app(MultiProjectCustomersQuery::class)->partnerQuery($om)->pluck('id')->all();

    expect($ids)->toContain($visibleCustomer->id)
        ->and($ids)->not->toContain($hiddenCustomer->id);
});

it('grants multi-project customers page permission to admin and operation manager only', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $permission = 'page_project_multi_project_customers';

    expect(Permission::query()->where('name', $permission)->exists())->toBeTrue();

    foreach (['Admin', 'Operation Manager'] as $roleName) {
        $role = Role::query()->where('name', $roleName)->firstOrFail();
        expect($role->hasPermissionTo($permission))->toBeTrue();
    }

    expect(Role::query()->where('name', 'Staff')->firstOrFail()->hasPermissionTo($permission))->toBeFalse()
        ->and(Role::query()->where('name', 'HRD')->firstOrFail()->hasPermissionTo($permission))->toBeFalse();
});
