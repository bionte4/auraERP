<?php

require_once __DIR__.'/../Helpers/CompanyHelper.php';
require_once __DIR__.'/../Helpers/SecurityHelper.php';
require_once __DIR__.'/../Helpers/TestBootstrapHelper.php';

use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\WorkLocation;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;
use Webkul\Support\Filament\Pages\WelcomeDashboard;
use Webkul\Support\Models\Company;
use Webkul\Support\Services\CompanyContext;

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('projects');
    TestBootstrapHelper::ensurePluginInstalled('employees');
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

    expect($titles)->toContain('Total Project', 'Total Employee', 'Total Revenue')
        ->and($titles)->not->toContain('Total Work Orders', 'Active Customers');
});

it('sums project operational_cost into total revenue opex detail', function () {
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
        'name'             => 'Revenue Project A',
        'company_id'       => $company->id,
        'is_active'        => true,
        'project_value'    => 1_000_000,
        'operational_cost' => 250_000,
    ]);
    Project::factory()->create([
        'name'             => 'Revenue Project B',
        'company_id'       => $company->id,
        'is_active'        => true,
        'project_value'    => 500_000,
        'operational_cost' => 100_000,
    ]);

    app()->forgetInstance(CompanyContext::class);

    $page = app(WelcomeDashboard::class);
    $revenueKpi = collect($page->getDashboardData()['kpis'] ?? [])
        ->firstWhere('title', 'Total Revenue');

    expect($revenueKpi['raw_value'] ?? null)->toBe(1_500_000.0)
        ->and($revenueKpi['detail'] ?? '')->toContain('OpEx:')
        ->and($revenueKpi['detail'] ?? '')->toContain('Margin:');
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

it('counts customer projects per row and unique assigned employees for welcome kpis', function (): void {
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

    $customer = Partner::factory()->create([
        'name'       => 'Welcome Customer',
        'company_id' => $company->id,
    ]);

    $withCustomerA = Project::factory()->create([
        'name'          => 'Customer Project A',
        'company_id'    => $company->id,
        'partner_id'    => $customer->id,
        'is_active'     => true,
        'project_value' => 1000,
    ]);
    $withCustomerB = Project::factory()->create([
        'name'          => 'Customer Project B',
        'company_id'    => $company->id,
        'partner_id'    => $customer->id,
        'is_active'     => true,
        'project_value' => 500,
    ]);
    Project::factory()->create([
        'name'          => 'Internal No Customer',
        'company_id'    => $company->id,
        'partner_id'    => null,
        'is_active'     => true,
        'project_value' => 9999,
    ]);

    $shared = Employee::create([
        'name'       => 'Shared Assignee',
        'company_id' => $company->id,
        'is_active'  => true,
    ]);
    $onlyB = Employee::create([
        'name'       => 'Only B Assignee',
        'company_id' => $company->id,
        'is_active'  => true,
    ]);
    Employee::create([
        'name'       => 'Unassigned Staff',
        'company_id' => $company->id,
        'is_active'  => true,
    ]);

    $withCustomerA->employees()->syncWithoutDetaching([$shared->id]);
    $withCustomerB->employees()->syncWithoutDetaching([$shared->id, $onlyB->id]);

    app()->forgetInstance(CompanyContext::class);

    $page = app(WelcomeDashboard::class);
    $data = $page->getDashboardData();
    $projectKpi = collect($data['kpis'] ?? [])->firstWhere('title', 'Total Project');
    $revenueKpi = collect($data['kpis'] ?? [])->firstWhere('title', 'Total Revenue');
    $employeeKpi = collect($data['kpis'] ?? [])->firstWhere('title', 'Total Employee');

    expect($projectKpi['raw_value'] ?? null)->toBe(2)
        ->and($projectKpi['detail'] ?? '')->toContain('1')
        ->and($revenueKpi['raw_value'] ?? null)->toBe(1500.0)
        ->and($employeeKpi['raw_value'] ?? null)->toBe(2);
});

it('maps employee placement markers from work locations with coordinates', function () {
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

    $jakarta = WorkLocation::factory()->create([
        'name'       => 'Kantor Jakarta',
        'city'       => 'Jakarta',
        'company_id' => $company->id,
        'latitude'   => -6.2088,
        'longitude'  => 106.8456,
        'is_active'  => true,
    ]);
    WorkLocation::factory()->create([
        'name'       => 'Tanpa Koordinat',
        'city'       => 'Bandung',
        'company_id' => $company->id,
        'latitude'   => null,
        'longitude'  => null,
        'is_active'  => true,
    ]);

    $budi = Employee::create([
        'name'             => 'Budi Penempatan',
        'company_id'       => $company->id,
        'work_location_id' => $jakarta->id,
        'is_active'        => true,
    ]);
    $siti = Employee::create([
        'name'             => 'Siti Penempatan',
        'company_id'       => $company->id,
        'work_location_id' => $jakarta->id,
        'is_active'        => true,
    ]);

    $customerProject = Project::factory()->create([
        'name'       => 'Map Customer Project',
        'company_id' => $company->id,
        'is_active'  => true,
    ]);
    $customerProject->employees()->syncWithoutDetaching([$budi->id, $siti->id]);

    app()->forgetInstance(CompanyContext::class);

    $page = app(WelcomeDashboard::class);
    $placements = $page->getDashboardData()['sites'] ?? [];

    $jakartaMarker = collect($placements['markers'] ?? [])->firstWhere('name', 'Jakarta');

    expect($placements['total'] ?? 0)->toBe(2)
        ->and($placements['cities'] ?? 0)->toBe(1)
        ->and(collect($placements['markers'] ?? [])->pluck('name')->all())->toContain('Jakarta')
        ->and($jakartaMarker['count'] ?? 0)->toBe(2)
        ->and($jakartaMarker['latitude'] ?? null)->toBeFloat()
        ->and($jakartaMarker['longitude'] ?? null)->toBeFloat()
        ->and(collect($placements['markers'] ?? [])->pluck('name')->all())->not->toContain('Bandung');
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
