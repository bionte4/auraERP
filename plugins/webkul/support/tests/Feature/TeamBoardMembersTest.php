<?php

use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectEmployeeAssigner;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;
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

it('shows clickable member total with employees involved in matched projects', function (): void {
    $manager = User::withoutEvents(fn () => User::factory()->create(['name' => 'Board PM']));
    Employee::create([
        'name'      => 'Board PM',
        'user_id'   => $manager->id,
        'is_active' => true,
    ]);
    $memberUser = User::withoutEvents(fn () => User::factory()->create(['name' => 'Board Member User']));
    $partner = Partner::factory()->create();
    $memberUser->forceFill(['partner_id' => $partner->id])->save();

    $employee = Employee::create([
        'name'       => 'Board Assigned Employee',
        'user_id'    => $memberUser->id,
        'partner_id' => $partner->id,
        'is_active'  => true,
    ]);

    Team::query()->create(['name' => 'Board Team Alpha']);

    $project = Project::factory()->create([
        'name'    => 'Board Team Alpha',
        'user_id' => $manager->id,
    ]);

    app(ProjectEmployeeAssigner::class)->sync($project, [$employee->id], $manager->id);

    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate([
            'name'       => $adminRoleName,
            'guard_name' => $guard,
        ]);
    }

    $viewer = FilamentHelper::actingAs();
    $viewer->syncRoles([$adminRoleName]);

    Livewire::test(TeamBoard::class)
        ->assertOk()
        ->assertSee('Board Team Alpha')
        ->assertSee('Board Assigned Employee')
        ->assertSee('Karyawan terlibat')
        ->assertSeeHtml('team-members-label is-clickable');

    $page = new TeamBoard;
    $card = collect($page->getBoardData()['cards'])->firstWhere('name', 'Board Team Alpha');

    expect($card)->not->toBeNull()
        ->and($card['member_count'])->toBeGreaterThanOrEqual(2)
        ->and(collect($card['members_all'])->pluck('name')->all())
        ->toContain('Board PM', 'Board Assigned Employee');
});

it('lists assigned employees without a login account and omits login-only team users', function (): void {
    $manager = User::withoutEvents(fn () => User::factory()->create(['name' => 'Board PM Login']));
    $managerEmployee = Employee::create([
        'name'      => 'Board PM Login',
        'user_id'   => $manager->id,
        'is_active' => true,
    ]);
    $ghost = Employee::create([
        'name'      => 'Field Worker No Login',
        'user_id'   => null,
        'is_active' => true,
    ]);
    $loginOnly = User::withoutEvents(fn () => User::factory()->create(['name' => 'Login Only Account']));

    $team = Team::query()->create(['name' => 'Field Crew']);
    $loginOnly->teams()->attach($team->id);

    $project = Project::factory()->create([
        'name'    => 'Field Crew',
        'user_id' => $manager->id,
    ]);

    app(ProjectEmployeeAssigner::class)->sync($project, [$managerEmployee->id, $ghost->id], $manager->id);

    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');
    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate(['name' => $adminRoleName, 'guard_name' => $guard]);
    }

    $viewer = FilamentHelper::actingAs();
    $viewer->syncRoles([$adminRoleName]);

    $page = new TeamBoard;
    $card = collect($page->getBoardData()['cards'])->firstWhere('name', 'Field Crew');

    expect($card)->not->toBeNull()
        ->and(collect($card['members_all'])->pluck('name')->all())
        ->toContain('Board PM Login', 'Field Worker No Login')
        ->and(collect($card['members_all'])->pluck('name')->all())
        ->not->toContain('Login Only Account')
        ->and(collect($card['members_all'])->firstWhere('name', 'Field Worker No Login')['has_login'])->toBeFalse();
});

it('lets managers add and remove assigned employees from the team board', function (): void {
    $manager = User::withoutEvents(fn () => User::factory()->create(['name' => 'Edit PM']));
    $existing = Employee::create(['name' => 'Already On Project', 'is_active' => true]);
    $incoming = Employee::create(['name' => 'Incoming Field Staff', 'is_active' => true]);

    Team::query()->create(['name' => 'Edit Crew']);
    $project = Project::factory()->create([
        'name'    => 'Edit Crew',
        'user_id' => $manager->id,
    ]);
    app(ProjectEmployeeAssigner::class)->sync($project, [$existing->id], $manager->id);

    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');
    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate(['name' => $adminRoleName, 'guard_name' => $guard]);
    }

    $viewer = FilamentHelper::actingAs();
    $viewer->syncRoles([$adminRoleName]);

    $page = Livewire::test(TeamBoard::class);
    $teamId = (int) collect((new TeamBoard)->getBoardData()['cards'])->firstWhere('name', 'Edit Crew')['id'];

    $page->call('startEditingMembers', $teamId)
        ->set('addEmployeeId', $incoming->id)
        ->call('addEmployeeToTeam');

    expect($project->fresh()->employees()->pluck('employees_employees.id')->all())
        ->toContain($existing->id, $incoming->id);

    $page->call('removeEmployeeFromTeam', $teamId, $existing->id);

    expect($project->fresh()->employees()->pluck('employees_employees.id')->all())
        ->toContain($incoming->id)
        ->and($project->fresh()->employees()->pluck('employees_employees.id')->all())
        ->not->toContain($existing->id);
});
