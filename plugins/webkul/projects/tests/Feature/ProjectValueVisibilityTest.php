<?php

use Spatie\Permission\Models\Role;
use Webkul\Project\Http\Resources\V1\ProjectResource as ProjectApiResource;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\OperationalScope;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function userWithProjectRoles(array $roleNames): User
{
    $user = FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
    ]);

    $roles = collect($roleNames)
        ->map(fn (string $name) => Role::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']))
        ->all();
    $user->syncRoles($roles);
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();

    return $user;
}

it('reserves contract value visibility for admin and the budget-control team', function (): void {
    $scope = app(OperationalScope::class);

    userWithProjectRoles(['Operation Manager']);
    expect($scope->canViewProjectValue())->toBeFalse();

    userWithProjectRoles(['HRD']);
    expect($scope->canViewProjectValue())->toBeFalse();

    userWithProjectRoles(['Staff']);
    expect($scope->canViewProjectValue())->toBeFalse();

    userWithProjectRoles(['Accounting']);
    expect($scope->canViewProjectValue())->toBeTrue();

    userWithProjectRoles(['Accounting Manager']);
    expect($scope->canViewProjectValue())->toBeTrue();

    userWithProjectRoles([config('filament-shield.panel_user.name', 'Admin')]);
    expect($scope->canViewProjectValue())->toBeTrue();
});

it('lets accounting managers edit the contract value from budget control but not accounting staff', function (): void {
    \Illuminate\Support\Facades\Mail::fake();

    $project = Project::factory()->create(['project_value' => 1000]);

    $manager = FilamentHelper::actingAs(['page_project_project_budget_control']);
    $managerRole = Role::query()->firstOrCreate(['name' => 'Accounting Manager', 'guard_name' => 'web']);
    $manager->syncRoles([$managerRole]);
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();

    \Livewire\Livewire::test(\Webkul\Project\Filament\Pages\ProjectBudgetControl::class)
        ->call('startEditValue', $project->id)
        ->assertSet('editingValueProjectId', $project->id)
        ->set('valueAmount', '888000000')
        ->call('saveValue')
        ->assertSet('editingValueProjectId', null);

    expect((float) $project->fresh()->project_value)->toBe(888_000_000.0);

    $staff = FilamentHelper::actingAs(['page_project_project_budget_control']);
    $staffRole = Role::query()->firstOrCreate(['name' => 'Accounting', 'guard_name' => 'web']);
    $staff->syncRoles([$staffRole]);
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();

    \Livewire\Livewire::test(\Webkul\Project\Filament\Pages\ProjectBudgetControl::class)
        ->call('startEditValue', $project->id)
        ->assertSet('editingValueProjectId', null)
        ->set('editingValueProjectId', $project->id)
        ->set('valueAmount', '1')
        ->call('saveValue');

    expect((float) $project->fresh()->project_value)->toBe(888_000_000.0);
});

it('omits the contract value from the project API for operational roles', function (): void {
    $project = Project::factory()->create(['project_value' => 777_000_000]);

    userWithProjectRoles(['Operation Manager']);

    $omData = (new ProjectApiResource($project))->toResponse(request())->getData(true)['data'];
    expect($omData)->not->toHaveKey('project_value');

    userWithProjectRoles(['Accounting Manager']);

    $financeData = (new ProjectApiResource($project))->toResponse(request())->getData(true)['data'];
    expect($financeData)->toHaveKey('project_value')
        ->and((float) $financeData['project_value'])->toBe(777_000_000.0);
});
