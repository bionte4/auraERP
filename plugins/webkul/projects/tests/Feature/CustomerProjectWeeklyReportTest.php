<?php

use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Exports\CustomerProjectWeeklyExport;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Project\Services\CustomerProjectWeeklyReportData;
use Webkul\Security\Models\User;
use Webkul\Security\Services\RolePermissionSynchronizer;

require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function makeCustomerReportUser(string $roleName): User
{
    $user = User::withoutEvents(fn () => User::factory()->create([
        'password'  => Hash::make('password'),
        'is_active' => true,
    ]));

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate([
            'name'       => $roleName,
            'guard_name' => $guard,
        ]);
    }

    $user->assignRole($roleName);

    return $user;
}

it('allows admin and operation manager but not hrd for customer project report', function (): void {
    $service = app(CustomerProjectWeeklyReportData::class);

    expect($service->viewerMayAccess(makeCustomerReportUser('Operation Manager')))->toBeTrue()
        ->and($service->viewerMayAccess(makeCustomerReportUser('HRD')))->toBeFalse()
        ->and($service->viewerMayAccess(makeCustomerReportUser('Admin')))->toBeTrue();
});

it('scopes project access for operation managers', function (): void {
    $om = makeCustomerReportUser('Operation Manager');
    $mine = Project::factory()->create(['is_active' => true, 'user_id' => $om->id, 'name' => 'Mine Project']);
    $other = Project::factory()->create(['is_active' => true, 'name' => 'Other Project']);

    $service = app(CustomerProjectWeeklyReportData::class);

    expect($service->canViewProject($mine, $om))->toBeTrue()
        ->and($service->canViewProject($other, $om))->toBeFalse();
});

it('masks task titles and omits hours from customer report payload', function (): void {
    $om = makeCustomerReportUser('Operation Manager');
    $this->actingAs($om);

    $partner = Partner::factory()->create(['name' => 'Acme Client']);
    $project = Project::factory()->create([
        'is_active'  => true,
        'user_id'    => $om->id,
        'partner_id' => $partner->id,
        'name'       => 'Acme Rollout',
    ]);

    $done = Task::query()->create([
        'project_id' => $project->id,
        'title'      => 'SECRET_INTERNAL_TASK_NAME',
        'state'      => TaskState::DONE->value,
        'is_active'  => true,
        'creator_id' => $om->id,
    ]);
    $done->forceFill(['updated_at' => now()])->saveQuietly();

    Task::query()->create([
        'project_id' => $project->id,
        'title'      => 'ANOTHER_SECRET_TITLE',
        'state'      => TaskState::IN_PROGRESS->value,
        'deadline'   => now()->subDay(),
        'is_active'  => true,
        'creator_id' => $om->id,
    ]);

    $report = app(CustomerProjectWeeklyReportData::class)->build($project->fresh(['partner', 'user']), $om, 'Weekly update');

    $payload = json_encode($report);

    expect($payload)->not->toContain('SECRET_INTERNAL_TASK_NAME')
        ->and($payload)->not->toContain('ANOTHER_SECRET_TITLE')
        ->and($payload)->not->toContain('overdue')
        ->and($payload)->not->toContain('jam')
        ->and($payload)->not->toContain('hours')
        ->and($report['cover']['customer'])->toBe('Acme Client')
        ->and($report['completed_rows'][0]['item'])->toStartWith('Deliverable #')
        ->and($report['open_rows'][0]['status'])->toBeIn(['Needs attention', 'Perlu perhatian'])
        ->and($report['customer_notes'])->toBe('Weekly update');

    $export = CustomerProjectWeeklyExport::make($project, $om, 'Weekly update');
    expect($export->sheets())->toHaveCount(5);
});

it('registers customer project weekly report permission for admin and om only', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $permission = 'page_project_customer_project_weekly_report';

    expect(Permission::query()->where('name', $permission)->exists())->toBeTrue();

    foreach (['Admin', 'Operation Manager'] as $roleName) {
        $role = Role::query()->where('name', $roleName)->firstOrFail();
        expect($role->hasPermissionTo($permission))->toBeTrue();
    }

    $hrd = Role::query()->where('name', 'HRD')->firstOrFail();
    expect($hrd->hasPermissionTo($permission))->toBeFalse();
});
