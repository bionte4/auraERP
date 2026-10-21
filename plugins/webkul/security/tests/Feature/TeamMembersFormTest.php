<?php

use Livewire\Livewire;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Models\Project;
use Webkul\Security\Filament\Resources\TeamResource\Pages\ManageTeams;
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

it('allows selecting employees when creating a team linked to a project', function (): void {
    FilamentHelper::actingAs([
        'view_any_security_team',
        'view_security_team',
        'create_security_team',
        'update_security_team',
    ]);

    $withLogin = User::withoutEvents(fn () => User::factory()->create(['name' => 'Login Member']));
    $employeeA = Employee::create(['name' => 'Team Employee A', 'user_id' => $withLogin->id, 'is_active' => true]);
    $employeeB = Employee::create(['name' => 'Team Employee B No Login', 'user_id' => null, 'is_active' => true]);
    $project = Project::factory()->create(['name' => 'Operations Squad']);

    Livewire::test(ManageTeams::class)
        ->callAction('create', data: [
            'name'       => 'Operations Squad',
            'project_id' => $project->id,
            'employees'  => [$employeeA->id, $employeeB->id],
        ])
        ->assertHasNoActionErrors();

    $team = Team::query()->where('name', 'Operations Squad')->first();

    expect($team)->not->toBeNull()
        ->and($project->fresh()->employees()->pluck('employees_employees.id')->all())
        ->toContain($employeeA->id, $employeeB->id)
        ->and($team->users()->pluck('users.id')->all())
        ->toContain($withLogin->id)
        ->and($team->users()->pluck('users.id')->all())
        ->not->toContain($employeeB->id);
});

it('allows updating assigned employees from the edit action', function (): void {
    FilamentHelper::actingAs([
        'view_any_security_team',
        'view_security_team',
        'create_security_team',
        'update_security_team',
    ]);

    $employee = Employee::create(['name' => 'New Team Employee', 'is_active' => true]);
    $project = Project::factory()->create(['name' => 'Existing Team']);
    $team = Team::query()->create([
        'name'       => 'Existing Team',
        'project_id' => $project->id,
    ]);

    Livewire::test(ManageTeams::class)
        ->callTableAction('edit', $team, data: [
            'name'       => 'Existing Team',
            'project_id' => $project->id,
            'employees'  => [$employee->id],
        ])
        ->assertHasNoTableActionErrors();

    expect($project->fresh()->employees()->pluck('employees_employees.id')->all())->toContain($employee->id);
});
