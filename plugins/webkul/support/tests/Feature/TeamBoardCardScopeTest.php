<?php

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
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

it('only shows the viewer assigned projects on team cards for operation managers', function (): void {
    $suffix = substr(uniqid(), -6);
    $teamName = 'Card Scope Team '.$suffix;

    $pmUser = User::withoutEvents(fn () => User::factory()->create(['name' => 'Card Scope PM '.$suffix]));

    $viewer = FilamentHelper::actingAs();
    $omRole = Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web']);
    $viewer->syncRoles([$omRole]);

    $partner = Partner::factory()->create();
    $viewer->forceFill(['partner_id' => $partner->id])->save();

    $employee = Employee::create([
        'name'       => 'Card Scope Viewer Employee '.$suffix,
        'user_id'    => $viewer->id,
        'partner_id' => $partner->id,
        'is_active'  => true,
    ]);

    $team = Team::query()->create(['name' => $teamName]);
    $team->users()->syncWithoutDetaching([$viewer->id, $pmUser->id]);

    $assignedProject = Project::factory()->create([
        'name'    => $teamName,
        'user_id' => $pmUser->id,
    ]);
    $fellowProject = Project::factory()->create([
        'name'    => 'Fellow Member Project '.$suffix,
        'user_id' => $pmUser->id,
    ]);

    DB::table('projects_project_employees')->insert([
        ['project_id' => $assignedProject->id, 'employee_id' => $employee->id],
    ]);

    $page = new TeamBoard;
    $card = collect($page->getBoardData()['cards'])->firstWhere('name', $teamName);

    expect($card)->not->toBeNull()
        ->and($card['project_count'])->toBe(1)
        ->and(collect($card['projects'])->pluck('id')->all())
        ->toContain($assignedProject->id)
        ->not->toContain($fellowProject->id);
});

it('shows all grouped projects on team cards for admin viewers', function (): void {
    $suffix = substr(uniqid(), -6);
    $teamName = 'Card Scope Admin Team '.$suffix;
    $fellowName = 'Admin Fellow Project '.$suffix;

    $pmUser = User::withoutEvents(fn () => User::factory()->create(['name' => 'Card Scope Admin PM '.$suffix]));

    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate([
            'name'       => $adminRoleName,
            'guard_name' => $guard,
        ]);
    }

    $viewer = FilamentHelper::actingAs();
    $viewer->syncRoles([$adminRoleName]);

    $team = Team::query()->create(['name' => $teamName]);
    $team->users()->syncWithoutDetaching([$pmUser->id]);

    Project::factory()->create([
        'name'    => $teamName,
        'user_id' => $pmUser->id,
    ]);
    Project::factory()->create([
        'name'    => $fellowName,
        'user_id' => $pmUser->id,
    ]);

    $page = new TeamBoard;
    $cards = collect($page->getBoardData()['cards']);
    $teamCard = $cards->firstWhere('name', $teamName);
    $fellowCard = $cards->firstWhere('name', $fellowName);

    expect($teamCard)->not->toBeNull()
        ->and($teamCard['project_count'])->toBe(1)
        ->and($fellowCard)->not->toBeNull()
        ->and($fellowCard['project_count'])->toBe(1);
});
