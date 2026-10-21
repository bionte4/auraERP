<?php

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role as SpatieRole;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Widgets\ProjectResponsibilityWidget;
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

function responsibilityQuery(): mixed
{
    $widget = new ProjectResponsibilityWidget;
    $reflection = new ReflectionMethod($widget, 'projectsQuery');
    $reflection->setAccessible(true);

    return $reflection->invoke($widget);
}

function makeResponsibilityTask(Project $project, TaskState $state, ?string $deadline = null): void
{
    $task = new Task;
    $task->forceFill([
        'title'      => 'Resp task '.$project->id.' '.$state->value.' '.uniqid(),
        'project_id' => $project->id,
        'state'      => $state,
        'deadline'   => $deadline,
        'priority'   => '0',
        'is_active'  => true,
    ])->save();
}

it('shows managed projects with leader, members, progress, and overdue counts for operation managers', function (): void {
    $omUser = FilamentHelper::actingAs([
        'widget_project_project_responsibility_widget',
        'view_any_project_project',
        'view_project_project',
    ]);
    $omRole = SpatieRole::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $omUser->syncRoles([$omRole]);
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();

    $suffix = substr(uniqid(), -6);
    $project = Project::factory()->create([
        'name'      => 'Resp OM Project '.$suffix,
        'user_id'   => $omUser->id,
        'is_active' => true,
        'end_date'  => now()->addDays(45),
    ]);
    $project->employees()->attach([
        Employee::factory()->create(['user_id' => null])->id,
        Employee::factory()->create(['user_id' => null])->id,
    ]);
    $otherProject = Project::factory()->create([
        'name'      => 'Resp Other Project '.$suffix,
        'is_active' => true,
    ]);

    makeResponsibilityTask($project, TaskState::DONE);
    makeResponsibilityTask($project, TaskState::IN_PROGRESS, now()->subDay()->toDateTimeString());
    makeResponsibilityTask($project, TaskState::IN_PROGRESS, now()->addDays(3)->toDateTimeString());
    makeResponsibilityTask($otherProject, TaskState::IN_PROGRESS);

    $rows = responsibilityQuery()->get()->keyBy('id');

    expect($rows->has($project->id))->toBeTrue()
        ->and($rows->has($otherProject->id))->toBeFalse();

    $row = $rows->get($project->id);

    expect((int) $row->members_count)->toBe(2)
        ->and((int) $row->total_tasks_count)->toBe(3)
        ->and((int) $row->completed_tasks_count)->toBe(1)
        ->and((int) $row->overdue_tasks_count)->toBe(1);

    $widget = new ProjectResponsibilityWidget;
    $columns = array_keys($widget->table(\Filament\Tables\Table::make($widget))->getColumns());

    expect($columns)->toContain('user.name', 'teamLeader.name', 'members_count', 'task_progress', 'overdue_tasks_count', 'deadline_status')
        ->not->toContain('project_value');
});

it('scopes team leaders to their led project and grants the widget via the rbac baseline', function (): void {
    $tlUser = FilamentHelper::actingAs([
        'widget_project_project_responsibility_widget',
        'view_any_project_project',
        'view_project_project',
    ]);
    $tlRole = SpatieRole::query()->firstOrCreate(['name' => 'Team Leader', 'guard_name' => 'web']);
    $tlUser->syncRoles([$tlRole]);
    app(\Webkul\Security\PermissionRegistrar::class)->forgetCachedPermissions();

    $leader = Employee::factory()->create(['user_id' => $tlUser->id]);
    $suffix = substr(uniqid(), -6);
    $ledProject = Project::factory()->create([
        'name'                    => 'Resp Led Project '.$suffix,
        'team_leader_employee_id' => $leader->id,
        'is_active'               => true,
    ]);
    $otherProject = Project::factory()->create([
        'name'      => 'Resp Not Led '.$suffix,
        'is_active' => true,
    ]);

    $visibleIds = responsibilityQuery()->pluck('id')->all();

    expect($visibleIds)->toContain($ledProject->id)
        ->not->toContain($otherProject->id);

    // The synchronizer baseline grants the widget to Operation Manager
    // and Team Leader (the permission row itself is created by
    // shield:generate in production; create it here like it would).
    foreach (['web', 'sanctum'] as $guard) {
        Permission::query()->firstOrCreate([
            'name'       => 'widget_project_project_responsibility_widget',
            'guard_name' => $guard,
        ]);
    }

    app(RolePermissionSynchronizer::class)->syncAll(false);

    foreach (['Operation Manager', 'Team Leader'] as $roleName) {
        $role = Role::query()->where('name', $roleName)->where('guard_name', 'web')->firstOrFail();

        expect($role->hasPermissionTo('widget_project_project_responsibility_widget', 'web'))->toBeTrue();
    }
});
