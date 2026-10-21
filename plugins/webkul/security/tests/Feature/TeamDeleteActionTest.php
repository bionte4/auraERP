<?php

use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Webkul\Security\Filament\Resources\TeamResource\Pages\ManageTeams;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('lets an authorized user delete a team that still has members after confirmation', function (): void {
    FilamentHelper::actingAs([
        'view_any_security_team',
        'view_security_team',
        'delete_security_team',
    ]);

    $member = User::factory()->create();
    $team = Team::query()->create(['name' => 'Person Named Team '.substr(uniqid(), -6)]);
    $team->users()->attach($member->id);

    expect($team->users()->count())->toBe(1);

    Livewire::test(ManageTeams::class)
        ->assertTableActionVisible('delete', $team)
        ->callTableAction('delete', $team);

    expect(Team::query()->find($team->id))->toBeNull()
        ->and(DB::table('user_team')->where('team_id', $team->id)->count())->toBe(0);
});

it('hides the delete action from users without the delete permission', function (): void {
    FilamentHelper::actingAs([
        'view_any_security_team',
        'view_security_team',
    ]);

    $team = Team::query()->create(['name' => 'Guarded Team '.substr(uniqid(), -6)]);

    Livewire::test(ManageTeams::class)
        ->assertTableActionHidden('delete', $team);
});
