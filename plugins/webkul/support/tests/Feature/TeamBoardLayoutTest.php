<?php

use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectEmployeeAssigner;
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

    Livewire::test(TeamBoard::class)
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

it('sorts team cards by name and member count and persists the sort choice', function (): void {
    boardAdminViewer();

    $suffix = substr(uniqid(), -6);
    Team::query()->create(['name' => 'Sort Zulu '.$suffix]);
    $alpha = Team::query()->create(['name' => 'Sort Alpha '.$suffix]);
    $employee = Employee::create(['name' => 'Sort Member '.$suffix, 'is_active' => true]);
    $project = Project::factory()->create(['name' => 'Sort Alpha '.$suffix]);
    app(ProjectEmployeeAssigner::class)->sync($project, [$employee->id], auth()->id());

    $page = new TeamBoard;

    $ownNames = fn (TeamBoard $board): array => collect($board->getBoardData()['cards'])
        ->pluck('name')
        ->filter(fn (string $name): bool => str_contains($name, $suffix))
        ->values()
        ->all();

    // Default sort: name A–Z, regardless of creation order.
    expect($ownNames($page))->toBe(['Sort Alpha '.$suffix, 'Sort Zulu '.$suffix]);

    $page->setSortBy('name_desc');
    expect($page->sortBy)->toBe('name_desc')
        ->and(session('team-board.sort'))->toBe('name_desc')
        ->and($ownNames($page))->toBe(['Sort Zulu '.$suffix, 'Sort Alpha '.$suffix]);

    // Alpha has one assigned employee, Zulu none.
    $page->setSortBy('members_desc');
    expect($ownNames($page))->toBe(['Sort Alpha '.$suffix, 'Sort Zulu '.$suffix]);

    // Unknown sorts fall back to name A–Z and are not persisted as-is.
    $page->setSortBy('bogus');
    expect($page->sortBy)->toBe('name_asc')
        ->and(session('team-board.sort'))->toBe('name_asc');

    session(['team-board.sort' => 'projects_desc']);
    $fresh = new TeamBoard;
    $fresh->mount();
    expect($fresh->sortBy)->toBe('projects_desc');

    session(['team-board.sort' => 'bogus']);
    $fresh->mount();
    expect($fresh->sortBy)->toBe('name_asc');
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
