<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Models\Project;
use Webkul\Security\Bouncer;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\TimeOff\Enums\LeaveValidationType;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Models\Leave;
use Webkul\TimeOff\Models\LeaveType;
use Webkul\TimeOff\Services\ApprovalEscalationService;

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('employees');
    TestBootstrapHelper::ensurePluginInstalled('projects');
    TestBootstrapHelper::ensurePluginInstalled('time-off');
    Bouncer::flushCache();
});

it('grants hierarchical access leveling across Director -> Manager -> Staff and supports one PM managing multiple projects', function (): void {
    $directorUser = User::factory()->create([
        'name'                => 'Direktur Utama',
        'resource_permission' => PermissionType::GROUP,
    ]);

    $managerUser = User::factory()->create([
        'name'                => 'Project Manager Senior',
        'resource_permission' => PermissionType::GROUP,
    ]);

    $staffUser = User::factory()->create([
        'name'                => 'Staff Engineer',
        'resource_permission' => PermissionType::INDIVIDUAL,
    ]);

    $otherUser = User::factory()->create([
        'name'                => 'Staff Divisi Lain',
        'resource_permission' => PermissionType::INDIVIDUAL,
    ]);

    $directorEmp = Employee::factory()->create([
        'name'      => 'Direktur Utama',
        'user_id'   => $directorUser->id,
        'parent_id' => null,
        'is_active' => true,
    ]);

    $managerEmp = Employee::factory()->create([
        'name'      => 'Project Manager Senior',
        'user_id'   => $managerUser->id,
        'parent_id' => $directorEmp->id,
        'is_active' => true,
    ]);

    $department = Department::factory()->create([
        'name'       => 'Divisi Operasi & Proyek',
        'manager_id' => $managerEmp->id,
    ]);

    $staffEmp = Employee::factory()->create([
        'name'          => 'Staff Engineer',
        'user_id'       => $staffUser->id,
        'parent_id'     => $managerEmp->id,
        'department_id' => $department->id,
        'is_active'     => true,
    ]);

    // 1 Project Manager handling multiple projects
    $projectOne = Project::factory()->create([
        'name'       => 'Proyek Jaringan Fiber A',
        'user_id'    => $managerUser->id,
        'creator_id' => $managerUser->id,
    ]);

    $projectTwo = Project::factory()->create([
        'name'       => 'Proyek Data Center B',
        'user_id'    => $managerUser->id,
        'creator_id' => $managerUser->id,
    ]);

    $staffProject = Project::factory()->create([
        'name'       => 'Proyek Internal Staff',
        'user_id'    => $staffUser->id,
        'creator_id' => $staffUser->id,
    ]);

    $otherProject = Project::factory()->create([
        'name'       => 'Proyek Divisi Lain',
        'user_id'    => $otherUser->id,
        'creator_id' => $otherUser->id,
    ]);

    // Verify Subordinate Resolution
    expect($managerEmp->getSubordinateUserIds(true))->toContain($staffUser->id)
        ->and($directorEmp->getSubordinateUserIds(true))->toContain($managerUser->id, $staffUser->id);

    // 1. Staff (INDIVIDUAL) only gets their own ID in Bouncer
    $this->actingAs($staffUser);
    Bouncer::flushCache();
    $staffAllowedUserIds = bouncer()->getAuthorizedUserIds();
    expect($staffAllowedUserIds)->toBe([$staffUser->id]);

    $staffVisibleProjectIds = Project::withoutGlobalScopes()
        ->whereIn('user_id', $staffAllowedUserIds)
        ->pluck('id')
        ->all();
    expect($staffVisibleProjectIds)->toContain($staffProject->id)
        ->and($staffVisibleProjectIds)->not->toContain($projectOne->id, $otherProject->id);

    // 2. Manager (GROUP via Org Hierarchy) gets both their own ID + subordinate Staff's ID, and manages multiple projects
    $this->actingAs($managerUser);
    Bouncer::flushCache();
    $managerAllowedUserIds = bouncer()->getAuthorizedUserIds();
    expect($managerAllowedUserIds)->toContain($managerUser->id, $staffUser->id)
        ->and($managerAllowedUserIds)->not->toContain($otherUser->id, $directorUser->id);

    $managerVisibleProjectIds = Project::withoutGlobalScopes()
        ->whereIn('user_id', $managerAllowedUserIds)
        ->pluck('id')
        ->all();
    expect($managerVisibleProjectIds)->toContain($projectOne->id, $projectTwo->id, $staffProject->id)
        ->and($managerVisibleProjectIds)->not->toContain($otherProject->id);

    // 3. Director (GROUP via Org Hierarchy) gets Manager's ID AND indirect subordinate Staff's ID
    $this->actingAs($directorUser);
    Bouncer::flushCache();
    $directorAllowedUserIds = bouncer()->getAuthorizedUserIds();
    expect($directorAllowedUserIds)->toContain($directorUser->id, $managerUser->id, $staffUser->id)
        ->and($directorAllowedUserIds)->not->toContain($otherUser->id);

    $directorVisibleProjectIds = Project::withoutGlobalScopes()
        ->whereIn('user_id', $directorAllowedUserIds)
        ->pluck('id')
        ->all();
    expect($directorVisibleProjectIds)->toContain($projectOne->id, $projectTwo->id, $staffProject->id)
        ->and($directorVisibleProjectIds)->not->toContain($otherProject->id);
});

it('routes multi-level permit approvals along the org hierarchy and auto-escalates overdue requests', function (): void {
    $directorUser = User::factory()->create(['name' => 'VP Operations']);
    $managerUser = User::factory()->create(['name' => 'Line Manager']);
    $staffUser = User::factory()->create(['name' => 'Staff Pemohon']);

    $directorEmp = Employee::factory()->create([
        'name'      => 'VP Operations',
        'user_id'   => $directorUser->id,
        'parent_id' => null,
        'is_active' => true,
    ]);

    $managerEmp = Employee::factory()->create([
        'name'      => 'Line Manager',
        'user_id'   => $managerUser->id,
        'parent_id' => $directorEmp->id,
        'is_active' => true,
    ]);

    $staffEmp = Employee::factory()->create([
        'name'      => 'Staff Pemohon',
        'user_id'   => $staffUser->id,
        'parent_id' => $managerEmp->id,
        'is_active' => true,
    ]);

    $this->actingAs($staffUser);

    $leaveType = LeaveType::query()->create([
        'name'                  => 'Izin Khusus Multi-Level',
        'leave_validation_type' => LeaveValidationType::BOTH,
        'requires_allocation'   => 'no',
        'employee_requests'     => 'yes',
        'time_type'             => 'leave',
        'request_unit'          => 'day',
    ]);

    $leave = Leave::query()->create([
        'user_id'           => $staffUser->id,
        'employee_id'       => $staffEmp->id,
        'holiday_status_id' => $leaveType->id,
        'private_name'      => 'Izin Keperluan Keluarga',
        'state'             => State::CONFIRM->value,
        'request_date_from' => now()->addDays(2)->toDateString(),
        'request_date_to'   => now()->addDays(3)->toDateString(),
        'date_from'         => now()->addDays(2)->toDateString(),
        'date_to'           => now()->addDays(3)->toDateString(),
        'number_of_days'    => 2,
        'duration_display'  => '2 working days',
    ]);

    // Approvers are automatically populated from the Employee reporting tree
    expect((int) $leave->manager_id)->toBe($managerEmp->id)
        ->and((int) $leave->first_approver_id)->toBe($managerEmp->id)
        ->and((int) $leave->second_approver_id)->toBe($directorEmp->id)
        ->and($leave->canBeApprovedBy($managerUser))->toBeTrue()
        ->and($leave->canBeApprovedBy($directorUser))->toBeTrue();

    // Step 1: Manager approves -> transitions from CONFIRM to VALIDATE_ONE
    $leave->approveBy($managerUser);
    $leave->refresh();
    expect($leave->state->value)->toBe(State::VALIDATE_ONE->value);

    // Step 2: Director approves -> transitions from VALIDATE_ONE to VALIDATE_TWO
    $leave->approveBy($directorUser);
    $leave->refresh();
    expect($leave->state->value)->toBe(State::VALIDATE_TWO->value);

    // Test Auto-Escalation when a request is overdue > 24 hours
    $overdueLeave = Leave::query()->create([
        'user_id'           => $staffUser->id,
        'employee_id'       => $staffEmp->id,
        'holiday_status_id' => $leaveType->id,
        'private_name'      => 'Izin Mendesak Belum Direspon Atasan',
        'state'             => State::CONFIRM->value,
        'request_date_from' => now()->addDays(5)->toDateString(),
        'request_date_to'   => now()->addDays(5)->toDateString(),
        'date_from'         => now()->addDays(5)->toDateString(),
        'date_to'           => now()->addDays(5)->toDateString(),
        'number_of_days'    => 1,
        'duration_display'  => '1 working day',
    ]);

    Leave::withoutGlobalScopes()
        ->whereKey($overdueLeave->id)
        ->update(['created_at' => now()->subHours(30)]);

    $result = app(ApprovalEscalationService::class)->escalateOverdueLeaves(24);
    $overdueLeave->refresh();

    expect($result['escalated'])->toBeGreaterThanOrEqual(1)
        ->and($overdueLeave->state->value)->toBe(State::VALIDATE_ONE->value)
        ->and($directorUser->notifications()->where('data->leave_escalation_id', $overdueLeave->id)->exists())->toBeTrue();
});
