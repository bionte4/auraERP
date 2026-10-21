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

function makeTaskForProject(Project $project): Task
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

it('scopes project and task lists to assigned projects for plain staff', function (): void {
    $staffUser = FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
        'view_any_project_task',
        'view_project_task',
    ]);
    $staffRole = Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
    $staffUser->syncRoles([$staffRole]);

    $employee = Employee::factory()->create(['user_id' => $staffUser->id]);

    $assignedProject = Project::factory()->create();
    $otherProject = Project::factory()->create();

    DB::table('projects_project_employees')->insert([
        ['project_id' => $assignedProject->id, 'employee_id' => $employee->id],
    ]);

    $assignedTask = makeTaskForProject($assignedProject);
    $otherTask = makeTaskForProject($otherProject);

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($assignedProject->id)
        ->not->toContain($otherProject->id);

    expect(TaskResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($assignedTask->id)
        ->not->toContain($otherTask->id);
});

it('scopes staff who are also operation managers to their assigned projects', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
    ]);
    $staffRole = Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $user->syncRoles([$staffRole, $omRole]);

    $project = Project::factory()->create();

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->not->toContain($project->id);
});

it('keeps the full project list for staff who are also accounting', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
    ]);
    $staffRole = Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'web']);
    $accountingRole = Role::query()->firstOrCreate(['name' => 'Accounting', 'guard_name' => 'web']);
    $user->syncRoles([$staffRole, $accountingRole]);

    $project = Project::factory()->create();

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($project->id);
});
