<?php

use Spatie\Permission\Models\Role;
use Webkul\Security\Models\Team;
use Webkul\Support\Filament\Pages\TeamBoard;

require_once __DIR__.'/../Helpers/FilamentHelper.php';
require_once __DIR__.'/../Helpers/SecurityHelper.php';
require_once __DIR__.'/../Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function boardAdminViewer(): void
{
    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate([
            'name'       => $adminRoleName,
            'guard_name' => $guard,
        ]);
    }

    $viewer = FilamentHelper::actingAs();
    $viewer->syncRoles([$adminRoleName]);
}

it('filters team cards by team or project name via the board search', function (): void {
    boardAdminViewer();

    $suffix = substr(uniqid(), -6);
    Team::query()->create(['name' => 'Layout Alpha '.$suffix]);
    Team::query()->create(['name' => 'Layout Beta '.$suffix]);

    $page = new TeamBoard;
    $page->search = 'Alpha '.$suffix;

    $cards = collect($page->getBoardData()['cards']);
    $names = $cards->pluck('name')->all();

    expect($page->getBoardData()['searching'])->toBeTrue()
        ->and($names)->toContain('Layout Alpha '.$suffix)
        ->not->toContain('Layout Beta '.$suffix);

    $page->search = 'zzz-no-match-'.$suffix;

    expect($page->getBoardData()['cards'])->toBe([]);
});

it('renders the board in cards, compact, and list modes with live search', function (): void {
    boardAdminViewer();

    $suffix = substr(uniqid(), -6);
    $teamName = 'Render Layout Team '.$suffix;
    Team::query()->create(['name' => $teamName]);

    \Livewire\Livewire::test(TeamBoard::class)
        ->assertOk()
        ->assertSee($teamName)
        ->call('setViewMode', 'compact')
        ->assertSet('viewMode', 'compact')
        ->assertSee($teamName)
        ->call('setViewMode', 'list')
        ->assertSet('viewMode', 'list')
        ->assertSee($teamName)
        ->set('search', 'no-such-team-'.$suffix)
        ->assertDontSee($teamName)
        ->set('search', '')
        ->assertSee($teamName);
});

it('accepts cards, compact, and list view modes, persists the choice, and rejects unknown modes', function (): void {
    boardAdminViewer();

    $page = new TeamBoard;

    $page->setViewMode('list');
    expect($page->viewMode)->toBe('list')
        ->and(session('team-board.view-mode'))->toBe('list');

    $page->setViewMode('compact');
    expect($page->viewMode)->toBe('compact');

    $page->setViewMode('bogus');
    expect($page->viewMode)->toBe('cards');

    session(['team-board.view-mode' => 'list']);
    $fresh = new TeamBoard;
    $fresh->mount();
    expect($fresh->viewMode)->toBe('list');

    session(['team-board.view-mode' => 'bogus']);
    $fresh->mount();
    expect($fresh->viewMode)->toBe('cards');
});
