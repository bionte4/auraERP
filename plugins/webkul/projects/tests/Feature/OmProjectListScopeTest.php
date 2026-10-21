<?php

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function makeOmScopeTask(Project $project): Task
{
    $task = new Task;
    $task->forceFill([
        'title'      => 'Task for '.$project->id,
        'project_id' => $project->id,
        'state'      => \Webkul\Project\Enums\TaskState::IN_PROGRESS,
        'priority'   => '0',
        'is_active'  => true,
    ])->save();

    return $task;
}

function actingAsOmUser()
{
    $user = FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
        'view_any_project_task',
        'view_project_task',
    ]);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $user->syncRoles([$omRole]);

    return $user;
}

it('scopes project and task lists to managed and assigned projects for operation managers', function (): void {
    $omUser = actingAsOmUser();

    $employee = Employee::factory()->create(['user_id' => $omUser->id]);

    $managedProject = Project::factory()->create(['user_id' => $omUser->id]);
    $memberProject = Project::factory()->create();
    $otherProject = Project::factory()->create();

    DB::table('projects_project_employees')->insert([
        ['project_id' => $memberProject->id, 'employee_id' => $employee->id],
    ]);

    $managedTask = makeOmScopeTask($managedProject);
    $memberTask = makeOmScopeTask($memberProject);
    $otherTask = makeOmScopeTask($otherProject);

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($managedProject->id, $memberProject->id)
        ->not->toContain($otherProject->id);

    expect(TaskResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($managedTask->id, $memberTask->id)
        ->not->toContain($otherTask->id);
});

it('shows empty project and task lists for operation managers without assignments', function (): void {
    actingAsOmUser();

    $project = Project::factory()->create();
    $task = makeOmScopeTask($project);

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())->toBe([])
        ->and(TaskResource::getEloquentQuery()->pluck('id')->all())->toBe([]);
});

it('keeps the full project list for accounting users', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
    ]);
    $accountingRole = Role::query()->firstOrCreate(['name' => 'Accounting', 'guard_name' => 'web']);
    $user->syncRoles([$accountingRole]);

    $project = Project::factory()->create();

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($project->id);
});

it('keeps the full project list for admin users', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
    ]);
    $adminRole = Role::query()->firstOrCreate([
        'name'       => config('filament-shield.panel_user.name', 'Admin'),
        'guard_name' => 'web',
    ]);
    $user->syncRoles([$adminRole]);

    $project = Project::factory()->create();

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($project->id);
});
