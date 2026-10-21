<?php

use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Project\Filament\Pages\ProjectBudgetControl;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;

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

function actingAsBudgetEditor(string $roleName): User
{
    $user = FilamentHelper::actingAs(['page_project_project_budget_control']);
    $role = Role::query()->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    $user->syncRoles([$role]);
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();

    return $user;
}

it('lets an accounting manager set the operational budget from the budget control page', function (): void {
    Mail::fake();

    $project = Project::factory()->create(['operational_cost' => 0]);

    actingAsBudgetEditor('Accounting Manager');

    Livewire::test(ProjectBudgetControl::class)
        ->call('startEditBudget', $project->id)
        ->assertSet('editingBudgetProjectId', $project->id)
        ->set('budgetAmount', '-10')
        ->call('saveBudget')
        ->assertHasErrors('budgetAmount')
        ->set('budgetAmount', '250000000')
        ->call('saveBudget')
        ->assertSet('editingBudgetProjectId', null);

    expect((float) $project->fresh()->operational_cost)->toBe(250_000_000.0);
});

it('does not let accounting staff change the operational budget', function (): void {
    Mail::fake();

    $project = Project::factory()->create(['operational_cost' => 1000]);

    actingAsBudgetEditor('Accounting');

    Livewire::test(ProjectBudgetControl::class)
        ->call('startEditBudget', $project->id)
        ->assertSet('editingBudgetProjectId', null)
        ->set('editingBudgetProjectId', $project->id)
        ->set('budgetAmount', '999')
        ->call('saveBudget');

    expect((float) $project->fresh()->operational_cost)->toBe(1000.0);
});
