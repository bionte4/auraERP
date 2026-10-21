<?php

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Filament\Widgets\EvaluationStatsOverviewWidget;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Performance\Models\PerformancePlan;
use Webkul\Project\Models\Project;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('performance');
    TestBootstrapHelper::ensurePluginInstalled('evaluations');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();

    $pkwtMigration = include __DIR__.'/../../database/migrations/2026_10_15_140000_add_pkwt_quarterly_fields_to_evaluations_tables.php';
    $pkwtMigration->up();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('renders the stats widget for a scoped operation manager and counts only in-scope performance plans', function (): void {
    $omUser = FilamentHelper::actingAs([]);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $omUser->syncRoles([$omRole]);
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();

    $omEmployee = Employee::factory()->create(['user_id' => $omUser->id]);
    $project = Project::factory()->create(['user_id' => $omUser->id]);

    $inScope = Employee::factory()->create(['user_id' => null]);
    $outScope = Employee::factory()->create(['user_id' => null]);

    DB::table('projects_project_employees')->insert([
        ['project_id' => $project->id, 'employee_id' => $omEmployee->id],
        ['project_id' => $project->id, 'employee_id' => $inScope->id],
    ]);

    Evaluation::factory()->create(['employee_id' => $inScope->id]);

    PerformancePlan::factory()->create(['employee_id' => $inScope->id]);
    PerformancePlan::factory()->create(['employee_id' => $outScope->id]);

    Livewire::test(EvaluationStatsOverviewWidget::class)->assertOk();

    $widget = new EvaluationStatsOverviewWidget;
    $reflection = new ReflectionMethod($widget, 'getStats');
    $reflection->setAccessible(true);
    $stats = $reflection->invoke($widget);

    $eligibilityStat = collect($stats)->first(
        fn ($stat): bool => str_contains((string) $stat->getLabel(), 'Kelayakan')
    );

    expect($eligibilityStat)->not->toBeNull()
        ->and((string) $eligibilityStat->getDescription())->toContain('(1 Program Goal)');
});
