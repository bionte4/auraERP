<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Webkul\Project\Filament\Pages\ProjectBudgetControl;
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

function insertBudgetMove(int $projectId, string $type, string $state, float $total, float $residual = 0): void
{
    $row = [
        'date'            => now()->toDateString(),
        'project_id'      => $projectId,
        'move_type'       => $type,
        'state'           => $state,
        'amount_total'    => $total,
        'amount_residual' => $residual,
    ];

    if (Schema::hasColumn('accounts_account_moves', 'name')) {
        $row['name'] = 'MOVE/'.$projectId.'/'.$type;
    }

    DB::table('accounts_account_moves')->insert($row);
}

it('computes invoicing, absorption, and status per project', function (): void {
    $suffix = substr(uniqid(), -6);

    $project = Project::factory()->create([
        'name'             => 'Budget Project '.$suffix,
        'project_value'    => 1_000_000_000,
        'operational_cost' => 100_000_000,
    ]);
    $noBudgetProject = Project::factory()->create([
        'name'             => 'Budget No Budget '.$suffix,
        'project_value'    => 500_000_000,
        'operational_cost' => 0,
    ]);

    insertBudgetMove($project->id, 'out_invoice', 'posted', 400_000_000, 150_000_000);
    insertBudgetMove($project->id, 'out_invoice', 'draft', 999_000_000);
    insertBudgetMove($project->id, 'in_invoice', 'posted', 85_000_000);
    insertBudgetMove($noBudgetProject->id, 'in_invoice', 'posted', 10_000_000);

    $rows = collect(app(ProjectBudgetControlData::class)->rows());

    $row = $rows->firstWhere('id', $project->id);
    expect($row)->not->toBeNull()
        ->and($row['invoiced'])->toBe(400_000_000.0)
        ->and($row['unpaid'])->toBe(150_000_000.0)
        ->and($row['invoiced_pct'])->toBe(40.0)
        ->and($row['vendor_cost'])->toBe(85_000_000.0)
        ->and($row['absorption_pct'])->toBe(85.0)
        ->and($row['remaining'])->toBe(15_000_000.0)
        ->and($row['status'])->toBe(ProjectBudgetControlData::STATUS_WARNING);

    $noBudgetRow = $rows->firstWhere('id', $noBudgetProject->id);
    expect($noBudgetRow)->not->toBeNull()
        ->and($noBudgetRow['status'])->toBe(ProjectBudgetControlData::STATUS_OVER)
        ->and($noBudgetRow['absorption_pct'])->toBeNull();
});

it('renders the budget control page for permitted users and denies others', function (): void {
    Mail::fake();

    $suffix = substr(uniqid(), -6);
    Project::factory()->create(['name' => 'Budget Page Project '.$suffix]);

    FilamentHelper::actingAs(['page_project_project_budget_control']);
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();

    expect(ProjectBudgetControl::canAccess())->toBeTrue();

    Livewire::test(ProjectBudgetControl::class)
        ->assertOk()
        ->assertSee('Budget Page Project '.$suffix);

    FilamentHelper::actingAs([]);
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();

    expect(ProjectBudgetControl::canAccess())->toBeFalse();
});
