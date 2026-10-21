<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Webkul\Project\Filament\Widgets\OperationalBudgetWidget;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectBudgetControlData;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    TestBootstrapHelper::ensurePluginInstalled('accounts');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function callWidgetMethod(OperationalBudgetWidget $widget, string $method, array $args = []): mixed
{
    $reflection = new ReflectionMethod($widget, $method);
    $reflection->setAccessible(true);

    return $reflection->invokeArgs($widget, $args);
}

it('scopes the operational budget widget to managed projects with opex figures and no contract value', function (): void {
    $omUser = FilamentHelper::actingAs([
        'widget_project_operational_budget_widget',
        'view_any_project_project',
        'view_project_project',
    ]);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $omUser->syncRoles([$omRole]);
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();

    $suffix = substr(uniqid(), -6);
    $project = Project::factory()->create([
        'name'             => 'OM Budget Widget '.$suffix,
        'user_id'          => $omUser->id,
        'project_value'    => 999_000_000,
        'operational_cost' => 100_000_000,
        'is_active'        => true,
    ]);
    $otherProject = Project::factory()->create([
        'name'      => 'Other OM Budget '.$suffix,
        'is_active' => true,
    ]);

    if (Schema::hasTable('accounts_account_moves') && Schema::hasColumn('accounts_account_moves', 'project_id')) {
        DB::table('accounts_account_moves')->insert([
            'date'            => now()->toDateString(),
            'project_id'      => $project->id,
            'move_type'       => 'in_invoice',
            'state'           => 'posted',
            'amount_total'    => 40_000_000,
            'amount_residual' => 0,
        ]);
    }

    $widget = new OperationalBudgetWidget;

    $visibleIds = callWidgetMethod($widget, 'projectsQuery')->pluck('id')->all();

    expect($visibleIds)->toContain($project->id)
        ->not->toContain($otherProject->id);

    $columns = array_keys($widget->table(\Filament\Tables\Table::make($widget))->getColumns());

    expect($columns)->toContain('operational_cost', 'vendor_cost', 'remaining', 'absorption')
        ->not->toContain('project_value');

    $row = callWidgetMethod($widget, 'budgetRow', [$project]);

    expect($row['budget'])->toBe(100_000_000.0)
        ->and($row['vendor_cost'])->toBe(40_000_000.0)
        ->and($row['remaining'])->toBe(60_000_000.0)
        ->and($row['absorption_pct'])->toBe(40.0)
        ->and($row['status'])->toBe(ProjectBudgetControlData::STATUS_OK)
        ->and($row)->not->toHaveKey('contract_value')
        ->and($row)->not->toHaveKey('project_value');
});
