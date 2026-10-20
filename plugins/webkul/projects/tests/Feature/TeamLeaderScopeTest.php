<?php

use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Resources\TaskResource;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\Role;
use Webkul\Security\Services\RolePermissionSynchronizer;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('scopes project and task queries to the single led project for team leaders', function (): void {
    $tlUser = FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
        'view_any_project_task',
        'view_project_task',
    ]);
    $tlRole = Role::query()->firstOrCreate(['name' => 'Team Leader', 'guard_name' => 'web']);
    $tlUser->syncRoles([$tlRole]);

    $leader = Employee::factory()->create([
        'name'    => 'Team Leader Employee',
        'user_id' => $tlUser->id,
    ]);

    $ledProject = Project::factory()->create([
        'team_leader_employee_id' => $leader->id,
    ]);
    $otherProject = Project::factory()->create();

    $makeTask = function (Project $project): Task {
        $task = new Task;
        $task->forceFill([
            'title'      => 'Task for '.$project->id,
            'project_id' => $project->id,
            'state'      => \Webkul\Project\Enums\TaskState::IN_PROGRESS,
            'priority'   => '0',
            'is_active'  => true,
        ])->save();

        return $task;
    };

    $ledTask = $makeTask($ledProject);
    $otherTask = $makeTask($otherProject);

    expect(ProjectResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($ledProject->id)
        ->not->toContain($otherProject->id);

    expect(TaskResource::getEloquentQuery()->pluck('id')->all())
        ->toContain($ledTask->id)
        ->not->toContain($otherTask->id);
});

it('prevents one employee from leading two active projects and auto-joins them as member', function (): void {
    $leader = Employee::factory()->create(['user_id' => null]);

    $first = Project::factory()->create([
        'team_leader_employee_id' => $leader->id,
        'is_active'               => true,
    ]);

    expect($first->employees()->whereKey($leader->id)->exists())->toBeTrue();

    expect(fn () => Project::factory()->create([
        'team_leader_employee_id' => $leader->id,
        'is_active'               => true,
    ]))->toThrow(ValidationException::class);
});

it('grants the team leader baseline permissions but not employee mutations', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $teamLeaderRole = Role::query()->where('name', 'Team Leader')->firstOrFail();

    foreach ([
        'view_any_project_project',
        'view_project_project',
        'view_any_project_task',
        'update_project_task',
        'view_any_project_timesheet',
        'create_project_timesheet',
        'update_project_timesheet',
        'view_any_employee_employee',
        'view_employee_employee',
    ] as $permission) {
        expect(Permission::query()->where('name', $permission)->exists())->toBeTrue();
        expect($teamLeaderRole->hasPermissionTo($permission))->toBeTrue();
    }

    foreach ([
        'create_employee_employee',
        'update_employee_employee',
        'delete_employee_employee',
        'create_project_project',
        'update_project_project',
        'delete_project_project',
    ] as $permission) {
        expect($teamLeaderRole->hasPermissionTo($permission))->toBeFalse();
    }
});

it('renders the org structure page with pm, team leader and members', function (): void {
    $user = FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
    ]);

    $leader = Employee::factory()->create(['name' => 'Lead Person', 'user_id' => null]);
    $member = Employee::factory()->create(['name' => 'Member Person', 'user_id' => null]);

    $project = Project::factory()->create([
        'user_id'                 => $user->id,
        'team_leader_employee_id' => $leader->id,
    ]);
    $project->employees()->syncWithoutDetaching([$member->id]);

    \Livewire\Livewire::test(
        \Webkul\Project\Filament\Resources\ProjectResource\Pages\ManageOrgStructure::class,
        ['record' => $project->id]
    )
        ->assertOk()
        ->assertSee('Lead Person')
        ->assertSee('Member Person');
});
