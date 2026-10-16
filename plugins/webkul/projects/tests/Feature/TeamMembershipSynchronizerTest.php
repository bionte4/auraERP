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
