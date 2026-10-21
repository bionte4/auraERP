<?php

use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Project\Filament\Pages\AssignProjectManagers;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Team;
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

function makeOmManagerUser(string $name): User
{
    $user = User::withoutEvents(fn () => User::factory()->create(['name' => $name]));
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $user->syncRoles([$omRole]);

    return $user;
}

it('assigns and clears project managers and syncs the new manager into the project team', function (): void {
    FilamentHelper::actingAs(['page_project_assign_project_managers']);
    $manager = makeOmManagerUser('Bulk PM One');

    $project = Project::factory()->create(['name' => 'Bulk PM Project '.substr(uniqid(), -6)]);

    expect(AssignProjectManagers::canAccess())->toBeTrue();

    Livewire::test(AssignProjectManagers::class)
        ->assertOk()
        ->call('assignManager', $project->id, (string) $manager->id);

    expect($project->fresh()->user_id)->toBe($manager->id);

    $team = Team::query()->withoutGlobalScopes()->where('name', $project->name)->first();
    expect($team)->not->toBeNull()
        ->and($team->users->pluck('id')->map(fn ($id): int => (int) $id)->all())->toContain($manager->id);

    Livewire::test(AssignProjectManagers::class)
        ->call('assignManager', $project->id, '');

    expect($project->fresh()->user_id)->toBeNull();
});

it('rejects users without the operation manager role as project manager', function (): void {
    FilamentHelper::actingAs(['page_project_assign_project_managers']);
    $staff = User::withoutEvents(fn () => User::factory()->create(['name' => 'Bulk PM Staff']));
    $staffRole = Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
    $staff->syncRoles([$staffRole]);

    $project = Project::factory()->create();
    $initialManagerId = $project->user_id;

    Livewire::test(AssignProjectManagers::class)
        ->call('assignManager', $project->id, (string) $staff->id);

    expect($project->fresh()->user_id)->toBe($initialManagerId)
        ->not->toBe($staff->id);
});

it('denies page access without the page permission even for operation managers', function (): void {
    $omUser = FilamentHelper::actingAs([]);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $omUser->syncRoles([$omRole]);

    expect(AssignProjectManagers::canAccess())->toBeFalse();
});

it('lists only operation managers in the manager summaries', function (): void {
    FilamentHelper::actingAs(['page_project_assign_project_managers']);
    $manager = makeOmManagerUser('Bulk PM Summary');
    Project::factory()->create(['user_id' => $manager->id]);

    $page = new AssignProjectManagers;
    $summary = collect($page->getManagerSummaries())->firstWhere('id', $manager->id);

    expect($summary)->not->toBeNull()
        ->and($summary['projects'])->toBeGreaterThanOrEqual(1);
});
