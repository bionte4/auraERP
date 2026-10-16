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
