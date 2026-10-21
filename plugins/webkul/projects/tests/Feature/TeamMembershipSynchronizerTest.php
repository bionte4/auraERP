<?php

use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectEmployeeAssigner;
use Webkul\Project\Services\TeamMembershipSynchronizer;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('syncs project manager and assigned employees into a team with the same name', function (): void {
    $manager = User::withoutEvents(fn () => User::factory()->create(['name' => 'Project Manager User']));
    $memberUser = User::withoutEvents(fn () => User::factory()->create(['name' => 'Assigned Employee User']));
    $partner = Partner::factory()->create();
    $memberUser->forceFill(['partner_id' => $partner->id])->save();

    $employee = Employee::create([
        'name'       => 'Assigned Employee',
        'user_id'    => $memberUser->id,
        'partner_id' => $partner->id,
        'is_active'  => true,
    ]);

    $team = Team::query()->create(['name' => 'Alpha Ops']);

    $project = Project::factory()->create([
        'name'    => 'Alpha Ops',
        'user_id' => $manager->id,
    ]);

    app(ProjectEmployeeAssigner::class)->sync($project, [$employee->id], $manager->id);

    $team->refresh();

    expect($team->users()->pluck('users.id')->all())
        ->toContain($manager->id, $memberUser->id);
});

it('mirrors the project roster: unassigned employees leave the team, non-employee accounts stay, login-less employees are reported', function (): void {
    $manager = User::withoutEvents(fn () => User::factory()->create(['name' => 'Mirror PM']));
    $userA = User::withoutEvents(fn () => User::factory()->create(['name' => 'Mirror User A']));
    $userB = User::withoutEvents(fn () => User::factory()->create(['name' => 'Mirror User B']));
    $manualUser = User::withoutEvents(fn () => User::factory()->create(['name' => 'Mirror Manual']));

    $employeeA = Employee::factory()->create(['name' => 'Mirror Employee A', 'user_id' => $userA->id]);
    $employeeB = Employee::factory()->create(['name' => 'Mirror Employee B', 'user_id' => $userB->id]);
    Employee::factory()->create(['name' => 'Mirror Employee No Login', 'user_id' => null]);
    $noLogin = Employee::query()->where('name', 'Mirror Employee No Login')->firstOrFail();

    $team = Team::query()->create(['name' => 'Mirror Ops']);
    $team->users()->attach([$manager->id, $userA->id, $userB->id, $manualUser->id]);

    $manualTeam = Team::query()->create(['name' => 'Mirror Manual Only']);
    $manualTeam->users()->attach([$userB->id]);

    $project = Project::factory()->create([
        'name'    => 'Mirror Ops',
        'user_id' => $manager->id,
    ]);

    app(ProjectEmployeeAssigner::class)->sync($project, [$employeeA->id, $noLogin->id], $manager->id);

    $memberIds = Team::query()->find($team->id)->users()->pluck('users.id')->all();

    expect($memberIds)->toContain($manager->id, $userA->id, $manualUser->id)
        ->not->toContain($userB->id);

    expect(app(TeamMembershipSynchronizer::class)->unsyncedEmployeeNamesForTeam($team))
        ->toContain('Mirror Employee No Login')
        ->not->toContain('Mirror Employee A');

    // Teams that resolve to no project are manual: sync never strips them.
    app(TeamMembershipSynchronizer::class)->syncForTeam($manualTeam);

    expect(Team::query()->find($manualTeam->id)->users()->pluck('users.id')->all())
        ->toContain($userB->id);
});

it('backfills memberships for existing projects via syncAll', function (): void {
    $manager = User::withoutEvents(fn () => User::factory()->create());
    Team::query()->create(['name' => 'Backfill Team']);

    Project::factory()->create([
        'name'    => 'Backfill Team',
        'user_id' => $manager->id,
    ]);

    app(TeamMembershipSynchronizer::class)->syncAll();

    $team = Team::query()->where('name', 'Backfill Team')->first();

    expect($team?->users()->pluck('users.id')->all())->toContain($manager->id);
});
