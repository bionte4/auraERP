<?php

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Models\Project;
use Webkul\Security\Filament\Resources\TeamResource;
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

it('scopes the teams list and editing to assigned projects for operation managers', function (): void {
    $omUser = FilamentHelper::actingAs([
        'view_any_security_team',
        'view_security_team',
        'create_security_team',
        'update_security_team',
    ]);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $omUser->syncRoles([$omRole]);

    $otherPm = User::factory()->create();

    $managedProject = Project::factory()->create(['user_id' => $omUser->id]);
    $memberProject = Project::factory()->create(['user_id' => $otherPm->id]);
    $foreignProject = Project::factory()->create(['user_id' => $otherPm->id]);

    $ownEmployee = Employee::factory()->create(['name' => 'OM Self', 'user_id' => $omUser->id]);

    DB::table('projects_project_employees')->insert([
        ['project_id' => $memberProject->id, 'employee_id' => $ownEmployee->id],
    ]);

    $managedTeam = Team::query()->create(['name' => 'Managed Team', 'project_id' => $managedProject->id, 'creator_id' => $otherPm->id]);
    $memberTeam = Team::query()->create(['name' => 'Member Team', 'project_id' => $memberProject->id, 'creator_id' => $otherPm->id]);
    $foreignTeam = Team::query()->create(['name' => 'Foreign Team', 'project_id' => $foreignProject->id, 'creator_id' => $otherPm->id]);
    $createdTeam = Team::query()->create(['name' => 'Created Team', 'creator_id' => $omUser->id]);

    $visibleIds = TeamResource::getEloquentQuery()->pluck('id')->all();

    expect($visibleIds)
        ->toContain($managedTeam->id, $memberTeam->id, $createdTeam->id)
        ->not->toContain($foreignTeam->id);

    // View follows the same scope.
    expect($omUser->can('view', $memberTeam))->toBeTrue()
        ->and($omUser->can('view', $foreignTeam))->toBeFalse();

    // Editing is narrower: managed-project teams and own creations only.
    expect($omUser->can('update', $managedTeam))->toBeTrue()
        ->and($omUser->can('update', $createdTeam))->toBeTrue()
        ->and($omUser->can('update', $memberTeam))->toBeFalse()
        ->and($omUser->can('update', $foreignTeam))->toBeFalse();
});

it('matches legacy unlinked teams by project name for view and edit scope', function (): void {
    $omUser = FilamentHelper::actingAs([
        'view_any_security_team',
        'view_security_team',
        'create_security_team',
        'update_security_team',
    ]);
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $omUser->syncRoles([$omRole]);

    $otherPm = User::factory()->create();

    $managedProject = Project::factory()->create(['name' => 'Legacy Named Project', 'user_id' => $omUser->id]);
    $memberProject = Project::factory()->create(['name' => 'Member Named Project', 'user_id' => $otherPm->id]);

    $ownEmployee = Employee::factory()->create(['name' => 'OM Self', 'user_id' => $omUser->id]);

    DB::table('projects_project_employees')->insert([
        ['project_id' => $memberProject->id, 'employee_id' => $ownEmployee->id],
    ]);

    // Legacy teams: no project_id, linked only by an identical name.
    $legacyManagedTeam = Team::query()->create(['name' => 'Legacy Named Project', 'project_id' => null, 'creator_id' => $otherPm->id]);
    $legacyMemberTeam = Team::query()->create(['name' => 'Member Named Project', 'project_id' => null, 'creator_id' => $otherPm->id]);
    $unrelatedTeam = Team::query()->create(['name' => 'Unrelated Legacy Team', 'project_id' => null, 'creator_id' => $otherPm->id]);

    $visibleIds = TeamResource::getEloquentQuery()->pluck('id')->all();

    expect($visibleIds)
        ->toContain($legacyManagedTeam->id, $legacyMemberTeam->id)
        ->not->toContain($unrelatedTeam->id);

    expect($omUser->can('update', $legacyManagedTeam))->toBeTrue()
        ->and($omUser->can('update', $legacyMemberTeam))->toBeFalse();
});

it('does not scope the teams list for hrd', function (): void {
    $hrdUser = FilamentHelper::actingAs([
        'view_any_security_team',
        'view_security_team',
        'update_security_team',
    ]);
    $hrdRole = Role::query()->firstOrCreate(['name' => 'HRD', 'guard_name' => 'web']);
    $hrdUser->syncRoles([$hrdRole]);

    $team = Team::query()->create(['name' => 'Any Team']);

    expect(TeamResource::getEloquentQuery()->pluck('id')->all())->toContain($team->id)
        ->and($hrdUser->can('update', $team))->toBeTrue();
});
