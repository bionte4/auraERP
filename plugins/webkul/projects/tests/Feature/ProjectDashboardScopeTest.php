<?php

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Filament\Widgets\ExpiringProjectsWidget;
use Webkul\Project\Models\Project;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('only shows assigned projects on the dashboard expiring widget for operation managers', function (): void {
    $omUser = FilamentHelper::actingAs([
        'widget_project_expiring_projects_widget',
        'view_project_project',
        'view_any_project_project',
    ]);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $omUser->syncRoles([$omRole]);

    $employee = Employee::factory()->create(['user_id' => $omUser->id]);

    $managed = Project::factory()->create([
        'name'      => 'OM Managed Expiring',
        'user_id'   => $omUser->id,
        'is_active' => true,
        'end_date'  => now()->addDays(5)->toDateString(),
    ]);
    $member = Project::factory()->create([
        'name'      => 'OM Member Expiring',
        'is_active' => true,
        'end_date'  => now()->addDays(6)->toDateString(),
    ]);
    $foreign = Project::factory()->create([
        'name'      => 'Foreign Expiring',
        'is_active' => true,
        'end_date'  => now()->addDays(7)->toDateString(),
    ]);

    DB::table('projects_project_employees')->insert([
        ['project_id' => $member->id, 'employee_id' => $employee->id],
    ]);

    Livewire::test(ExpiringProjectsWidget::class)
        ->assertOk()
        ->assertSee('OM Managed Expiring')
        ->assertSee('OM Member Expiring')
        ->assertDontSee('Foreign Expiring');
});

it('shows all expiring projects on the dashboard widget for hrd', function (): void {
    $hrdUser = FilamentHelper::actingAs([
        'widget_project_expiring_projects_widget',
        'view_project_project',
        'view_any_project_project',
    ]);
    $hrdRole = Role::query()->firstOrCreate(['name' => 'HRD', 'guard_name' => 'web']);
    $hrdUser->syncRoles([$hrdRole]);

    Project::factory()->create([
        'name'      => 'HRD Visible Expiring',
        'is_active' => true,
        'end_date'  => now()->addDays(5)->toDateString(),
    ]);

    Livewire::test(ExpiringProjectsWidget::class)
        ->assertOk()
        ->assertSee('HRD Visible Expiring');
});
