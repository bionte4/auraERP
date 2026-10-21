<?php

use Spatie\Permission\Models\Role;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectManagerBulkImportService;
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

function makePmBulkUser(string $name, string $email, string $roleName): User
{
    $user = User::withoutEvents(fn () => User::factory()->create(['name' => $name, 'email' => $email]));
    $role = Role::query()->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
    $user->syncRoles([$role]);

    return $user;
}

/**
 * @return array<int, array<int, mixed>>
 */
function pmBulkHeader(): array
{
    return [['project_id', 'project_name', 'customer', 'current_manager_email', 'new_manager_email']];
}

it('prefills the template with every project and its current manager email', function (): void {
    $suffix = substr(uniqid(), -6);
    $manager = makePmBulkUser('Bulk Template PM '.$suffix, 'bulk.template.'.$suffix.'@example.com', 'Operation Manager');
    $project = Project::factory()->create([
        'name'    => 'Bulk Template Project '.$suffix,
        'user_id' => $manager->id,
    ]);

    $rows = collect(app(ProjectManagerBulkImportService::class)->templateRows());
    $row = $rows->firstWhere(0, $project->id);

    expect($row)->not->toBeNull()
        ->and($row[3])->toBe($manager->email)
        ->and($row[4])->toBe($manager->email);
});

it('assigns a new manager by project id and email and syncs the team', function (): void {
    $suffix = substr(uniqid(), -6);
    $manager = makePmBulkUser('Bulk Import PM '.$suffix, 'bulk.import.'.$suffix.'@example.com', 'Operation Manager');
    $project = Project::factory()->create(['name' => 'Bulk Import Project '.$suffix]);

    $result = app(ProjectManagerBulkImportService::class)->importRows(array_merge(pmBulkHeader(), [
        [$project->id, $project->name, '', '', $manager->email],
    ]));

    expect($result['updated'])->toBe(1)
        ->and($result['errors'])->toBe([])
        ->and($project->fresh()->user_id)->toBe($manager->id);

    $team = Team::query()->withoutGlobalScopes()->where('name', $project->name)->first();
    expect($team->users->pluck('id')->map(fn ($id): int => (int) $id)->all())->toContain($manager->id);
});

it('clears the manager when the new email is empty and skips unchanged rows', function (): void {
    $suffix = substr(uniqid(), -6);
    $manager = makePmBulkUser('Bulk Clear PM '.$suffix, 'bulk.clear.'.$suffix.'@example.com', 'Operation Manager');
    $clearedProject = Project::factory()->create(['user_id' => $manager->id]);
    $keptProject = Project::factory()->create(['user_id' => $manager->id]);

    $result = app(ProjectManagerBulkImportService::class)->importRows(array_merge(pmBulkHeader(), [
        [$clearedProject->id, $clearedProject->name, '', $manager->email, ''],
        [$keptProject->id, $keptProject->name, '', $manager->email, $manager->email],
    ]));

    expect($result['updated'])->toBe(1)
        ->and($result['unchanged'])->toBe(1)
        ->and($clearedProject->fresh()->user_id)->toBeNull()
        ->and($keptProject->fresh()->user_id)->toBe($manager->id);
});

it('rejects unknown projects, unknown emails, and non-manager users with line errors', function (): void {
    $suffix = substr(uniqid(), -6);
    $staff = makePmBulkUser('Bulk Staff '.$suffix, 'bulk.staff.'.$suffix.'@example.com', 'Staff');
    $project = Project::factory()->create();

    $result = app(ProjectManagerBulkImportService::class)->importRows(array_merge(pmBulkHeader(), [
        [999999, 'Ghost Project '.$suffix, '', '', 'ghost.'.$suffix.'@example.com'],
        [$project->id, $project->name, '', '', 'ghost.'.$suffix.'@example.com'],
        [$project->id, $project->name, '', '', $staff->email],
    ]));

    expect($result['updated'])->toBe(0)
        ->and($result['skipped'])->toBe(3)
        ->and($result['errors'])->toHaveCount(3)
        ->and($result['errors'][0])->toContain('not found')
        ->and($result['errors'][2])->toContain('not an Operation Manager');
});

it('matches projects by exact name when the id is missing', function (): void {
    $suffix = substr(uniqid(), -6);
    $manager = makePmBulkUser('Bulk Name PM '.$suffix, 'bulk.name.'.$suffix.'@example.com', 'Operation Manager');
    $project = Project::factory()->create(['name' => 'Bulk Name Project '.$suffix]);

    $result = app(ProjectManagerBulkImportService::class)->importRows(array_merge(pmBulkHeader(), [
        ['', $project->name, '', '', $manager->email],
    ]));

    expect($result['updated'])->toBe(1)
        ->and($project->fresh()->user_id)->toBe($manager->id);
});
