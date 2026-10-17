<?php

require_once __DIR__.'/../../plugins/webkul/support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../plugins/webkul/support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../plugins/webkul/support/tests/Helpers/FilamentHelper.php';

use Spatie\Permission\Models\Role;
use Webkul\Security\Models\User;
use Webkul\Security\Support\AdminAccess;
use Webkul\Support\Filament\Pages\MyDayDashboard;
use Webkul\Support\Models\Company;

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
    Company::query()->first() ?? Company::factory()->create();
});

it('builds a richer my day payload with priorities and links for admins', function (): void {
    $adminRoleName = AdminAccess::adminRoleName();

    Role::query()->firstOrCreate([
        'name'       => $adminRoleName,
        'guard_name' => 'web',
    ]);

    $admin = User::withoutEvents(fn () => User::factory()->create([
        'name' => 'Admin Demo',
    ]));
    $admin->syncRoles([$adminRoleName]);

    $this->actingAs($admin);
    FilamentHelper::bootAdminPanel();

    $page = app(MyDayDashboard::class);
    $data = $page->getDayData();

    expect($data['greeting'])->toContain('Admin Demo')
        ->and($data['date_label'])->not->toBeEmpty()
        ->and($data['cards'])->not->toBeEmpty()
        ->and($data['cards'][0])->toHaveKeys(['label', 'value', 'href', 'color', 'hint', 'icon'])
        ->and($data['recommendations'])->not->toBeEmpty()
        ->and($data['recommendations'][0])->toHaveKeys(['title', 'body', 'href', 'color', 'cta'])
        ->and($data['links'])->not->toBeEmpty();
});
