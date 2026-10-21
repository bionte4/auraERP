<?php

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Filament\Widgets\EvaluationApprovalProgressWidget;
use Webkul\Evaluation\Models\Evaluation;
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

it('scopes the evaluation dashboard to assigned-project employees for operation managers', function (): void {
    $omUser = FilamentHelper::actingAs([]);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $omUser->syncRoles([$omRole]);

    $omEmployee = Employee::factory()->create(['user_id' => $omUser->id]);

    $project = Project::factory()->create(['user_id' => $omUser->id]);

    $inScope = Employee::factory()->create(['name' => 'In Scope Evaluatee', 'user_id' => null]);
    $outScope = Employee::factory()->create(['name' => 'Out Scope Evaluatee', 'user_id' => null]);

    DB::table('projects_project_employees')->insert([
        ['project_id' => $project->id, 'employee_id' => $omEmployee->id],
        ['project_id' => $project->id, 'employee_id' => $inScope->id],
    ]);

    Evaluation::factory()->create(['employee_id' => $inScope->id]);
    Evaluation::factory()->create(['employee_id' => $outScope->id]);

    Livewire::test(EvaluationApprovalProgressWidget::class)
        ->assertOk()
        ->assertSee('In Scope Evaluatee')
        ->assertDontSee('Out Scope Evaluatee');
});

it('shows all evaluations on the dashboard for hrd', function (): void {
    $hrdUser = FilamentHelper::actingAs([]);
    $hrdRole = Role::query()->firstOrCreate(['name' => 'HRD', 'guard_name' => 'web']);
    $hrdUser->syncRoles([$hrdRole]);

    $anyone = Employee::factory()->create(['name' => 'Anyone Evaluatee', 'user_id' => null]);
    Evaluation::factory()->create(['employee_id' => $anyone->id]);

    Livewire::test(EvaluationApprovalProgressWidget::class)
        ->assertOk()
        ->assertSee('Anyone Evaluatee');
});
