<?php

use Livewire\Livewire;
use Webkul\Security\Filament\Resources\TeamResource\Pages\ManageTeams;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';

beforeEach(function (): void {
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('allows selecting team members when creating a team', function (): void {
    FilamentHelper::actingAs([
        'view_any_security_team',
        'view_security_team',
        'create_security_team',
        'update_security_team',
    ]);

    $memberA = User::withoutEvents(fn () => User::factory()->create(['name' => 'Team Member A']));
    $memberB = User::withoutEvents(fn () => User::factory()->create(['name' => 'Team Member B']));

    Livewire::test(ManageTeams::class)
        ->callAction('create', data: [
            'name'  => 'Operations Squad',
            'users' => [$memberA->id, $memberB->id],
        ])
        ->assertHasNoActionErrors();

    $team = Team::query()->where('name', 'Operations Squad')->first();

    expect($team)->not->toBeNull()
        ->and($team->users()->pluck('users.id')->all())
        ->toContain($memberA->id, $memberB->id);
});

it('allows updating team members from the edit action', function (): void {
    FilamentHelper::actingAs([
        'view_any_security_team',
        'view_security_team',
        'create_security_team',
        'update_security_team',
    ]);

    $member = User::withoutEvents(fn () => User::factory()->create(['name' => 'New Team Member']));
    $team = Team::query()->create(['name' => 'Existing Team']);

    Livewire::test(ManageTeams::class)
        ->callTableAction('edit', $team, data: [
            'name'  => 'Existing Team',
            'users' => [$member->id],
        ])
        ->assertHasNoTableActionErrors();

    expect($team->fresh()->users()->pluck('users.id')->all())->toContain($member->id);
});
