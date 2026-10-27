<?php

use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Employee\Models\Employee;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Filament\Resources\ProjectResource\Pages\ListProjects;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectGovernance;
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

it('allows one customer to own multiple projects', function (): void {
    $customer = Partner::factory()->create(['name' => 'Bank Multi Project']);

    $first = Project::factory()->create([
        'name'       => 'Kontrak A',
        'partner_id' => $customer->id,
    ]);
    $second = Project::factory()->create([
        'name'       => 'Kontrak B',
        'partner_id' => $customer->id,
    ]);

    expect($first->partner_id)->toBe($customer->id)
        ->and($second->partner_id)->toBe($customer->id)
        ->and(Project::query()->where('partner_id', $customer->id)->count())->toBe(2);
});

it('allows one operation manager to handle many projects', function (): void {
    $om = User::withoutEvents(fn () => User::factory()->create(['name' => 'OM Multi']));
    $om->syncRoles([Role::query()->firstOrCreate(['name' => 'Operation Manager', 'guard_name' => 'web'])]);

    $first = Project::factory()->create(['user_id' => $om->id]);
    $second = Project::factory()->create(['user_id' => $om->id]);

    expect($first->user_id)->toBe($om->id)
        ->and($second->user_id)->toBe($om->id)
        ->and(ProjectGovernance::isOperationManager($om->id))->toBeTrue();
});

it('requires a project director when the project is strategic', function (): void {
    expect(fn () => Project::factory()->create([
        'is_strategic' => true,
    ]))->toThrow(ValidationException::class);

    $director = User::withoutEvents(fn () => User::factory()->create(['name' => 'Director One']));

    $project = Project::factory()->create([
        'is_strategic'        => true,
        'project_director_id' => $director->id,
    ]);

    expect($project->fresh()->director?->id)->toBe($director->id);
});

it('rejects a staff user as the governed project manager', function (): void {
    $staff = User::withoutEvents(fn () => User::factory()->create());
    $staff->syncRoles([Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => 'web'])]);

    expect(fn () => ProjectGovernance::assertProjectManager($staff->id))
        ->toThrow(ValidationException::class);
});

it('keeps team leader limited to one active project', function (): void {
    $leader = Employee::factory()->create(['user_id' => null]);

    Project::factory()->create([
        'team_leader_employee_id' => $leader->id,
        'is_active'               => true,
    ]);

    expect(fn () => Project::factory()->create([
        'team_leader_employee_id' => $leader->id,
        'is_active'               => true,
    ]))->toThrow(ValidationException::class);
});

it('renders the customer projects table without a server error', function (): void {
    FilamentHelper::actingAs([
        'view_any_project_project',
        'view_project_project',
    ]);

    $customer = Partner::factory()->create(['name' => 'List Customer']);
    Project::factory()->create([
        'name'       => 'Listed Customer Project',
        'partner_id' => $customer->id,
        'is_active'  => true,
    ]);

    Livewire::withQueryParams(['activeTableView' => 'customer_projects'])
        ->test(ListProjects::class)
        ->assertOk();
});
