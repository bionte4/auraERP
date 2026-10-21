<?php

use Spatie\Permission\Models\Role;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Enums\PermissionType;
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

function financeListTask(Project $project): Task
{
    $task = new Task;
    $task->forceFill([
        'title'      => 'Finance list task '.$project->id,
        'project_id' => $project->id,
        'state'      => \Webkul\Project\Enums\TaskState::IN_PROGRESS,
        'priority'   => '0',
        'is_active'  => true,
    ])->save();

    return $task;
}

function actingAsFinanceListUser(array $roleNames, PermissionType $resourcePermission): User
{
    $user = FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
        'view_any_project_task',
        'view_project_task',
    ]);

    $roles = collect($roleNames)
        ->map(fn (string $name) => Role::query()->firstOrCreate(['name' => $name, 'guard_name' => 'web']))
        ->all();
    $user->syncRoles($roles);

    $user->forceFill(['resource_permission' => $resourcePermission])->saveQuietly();

    return $user;
}

function projectOwnedBySomeoneElse(): Project
{
    $owner = User::withoutEvents(fn () => User::factory()->create());

    return Project::factory()->create([
        'user_id'    => $owner->id,
        'creator_id' => $owner->id,
    ]);
}

it('shows projects created by others to an accounting manager with group resource permission', function (): void {
    actingAsFinanceListUser(['Accounting Manager'], PermissionType::GROUP);

    $project = projectOwnedBySomeoneElse();
    $task = financeListTask($project);

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($project->id);

    expect(TaskResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($task->id);
});

it('keeps the full list for an accounting manager who also holds the staff role', function (): void {
    actingAsFinanceListUser(['Staff', 'Accounting Manager'], PermissionType::GROUP);

    $project = projectOwnedBySomeoneElse();

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($project->id);
});

it('keeps the full list for accounting staff with group resource permission', function (): void {
    actingAsFinanceListUser(['Accounting'], PermissionType::GROUP);

    $project = projectOwnedBySomeoneElse();

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($project->id);
});

it('still scopes plain staff with group resource permission to their assignments', function (): void {
    actingAsFinanceListUser(['Staff'], PermissionType::GROUP);

    $project = projectOwnedBySomeoneElse();

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->not->toContain($project->id);
});
