<?php

require_once __DIR__.'/../Helpers/CompanyHelper.php';
require_once __DIR__.'/../Helpers/SecurityHelper.php';
require_once __DIR__.'/../Helpers/TestBootstrapHelper.php';

use Spatie\Permission\Models\Role;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\WelcomeDashboard;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('projects');
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

it('counts projects with null company_id in total project kpi like the projects list', function () {
    $company = Company::factory()->create();
    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate([
            'name'       => $adminRoleName,
            'guard_name' => $guard,
        ]);
    }

    $admin = CompanyHelper::actingAsCompanyUser($company, ['bypass_company_scope']);
    $admin->syncRoles([$adminRoleName]);

    Project::factory()->create([
        'name'       => 'Company Scoped Project',
        'company_id' => $company->id,
        'is_active'  => true,
    ]);
    Project::factory()->create([
        'name'       => 'Unscoped Company Project',
        'company_id' => null,
        'is_active'  => true,
    ]);

    app()->forgetInstance(CompanyContext::class);

    $page = app(WelcomeDashboard::class);
    $projectKpi = collect($page->getDashboardData()['kpis'] ?? [])
        ->firstWhere('title', 'Total Project');

    expect($projectKpi['raw_value'] ?? null)->toBe(2);
});

it('includes null-company projects for company-scoped users without bypass', function () {
    $company = Company::factory()->create();
    $otherCompany = Company::factory()->create();
    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate([
            'name'       => $adminRoleName,
            'guard_name' => $guard,
        ]);
    }

    $user = CompanyHelper::actingAsCompanyUser($company);
    $user->syncRoles([$adminRoleName]);

    Project::factory()->create([
        'name'       => 'In Active Company',
        'company_id' => $company->id,
        'is_active'  => true,
    ]);
    Project::factory()->create([
        'name'       => 'Null Company',
        'company_id' => null,
        'is_active'  => true,
    ]);
    Project::factory()->create([
        'name'       => 'Other Company',
        'company_id' => $otherCompany->id,
        'is_active'  => true,
    ]);

    app()->forgetInstance(CompanyContext::class);

    $page = app(WelcomeDashboard::class);
    $projectKpi = collect($page->getDashboardData()['kpis'] ?? [])
        ->firstWhere('title', 'Total Project');

    expect($projectKpi['raw_value'] ?? null)->toBe(2);
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
