<?php

use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Webkul\Attendance\Models\AttendanceSite;
use Webkul\Pos\Models\PosOrder;
use Webkul\Pos\Models\PosRegister;
use Webkul\Pos\Services\PosService;
use Webkul\Security\Bouncer;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Filament\Resources\UserResource;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Security\Services\RolePermissionSynchronizer;

beforeEach(function (): void {
    $posMigration = include __DIR__.'/../../../pos/database/migrations/2026_10_15_100000_create_pos_tables.php';
    $posMigration->up();

    Bouncer::flushCache();
    app(RolePermissionSynchronizer::class)->syncAll(false);
});

it('synchronizes standard RBAC roles (Admin, HRD, Kasir, Staff) and assigns appropriate permissions', function (): void {
    $result = app(RolePermissionSynchronizer::class)->syncAll(false);

    expect($result['permissions_count'])->toBeGreaterThan(20)
        ->and($result['roles_synced'])->toContain('Admin', 'HRD', 'Kasir', 'Staff');

    $adminRole = Role::query()->where('name', 'Admin')->firstOrFail();
    $hrdRole = Role::query()->where('name', 'HRD')->firstOrFail();
    $kasirRole = Role::query()->where('name', 'Kasir')->firstOrFail();
    $staffRole = Role::query()->where('name', 'Staff')->firstOrFail();

    expect($adminRole->hasPermissionTo('view_any_security_user'))->toBeTrue()
        ->and($adminRole->hasPermissionTo('view_any_pos_pos::order'))->toBeTrue()
        ->and($hrdRole->hasPermissionTo('view_any_employee_employee'))->toBeTrue()
        ->and($hrdRole->hasPermissionTo('view_any_attendance_attendance::site'))->toBeTrue()
        ->and($kasirRole->hasPermissionTo('page_pos_pos_terminal_page'))->toBeTrue()
        ->and($kasirRole->hasPermissionTo('create_pos_pos::order'))->toBeTrue()
        ->and($kasirRole->hasPermissionTo('delete_security_user'))->toBeFalse()
        ->and($staffRole->hasPermissionTo('page_attendance_clock_page'))->toBeTrue()
        ->and($staffRole->hasPermissionTo('delete_any_pos_pos::order'))->toBeFalse();
});

it('enforces User CRUD protection against self-deletion, default user deletion, and removing the last Admin', function (): void {
    $adminRole = Role::query()->where('name', 'Admin')->firstOrFail();
    $staffRole = Role::query()->where('name', 'Staff')->firstOrFail();

    // Ensure only one admin exists for the last-admin check
    User::query()->each(fn (User $u) => $u->syncRoles([]));

    $adminUser = User::query()->firstOrCreate(
        ['email' => 'sole.admin@example.com'],
        [
            'name'                => 'Sole Admin',
            'password'            => bcrypt('password123'),
            'is_active'           => true,
            'is_default'          => true,
            'resource_permission' => PermissionType::GLOBAL,
        ]
    );
    $adminUser->syncRoles([$adminRole->id]);

    $regularUser = User::query()->firstOrCreate(
        ['email' => 'regular.staff@example.com'],
        [
            'name'                => 'Regular Staff',
            'password'            => bcrypt('password123'),
            'is_active'           => true,
            'is_default'          => false,
            'creator_id'          => $adminUser->id,
            'resource_permission' => PermissionType::INDIVIDUAL,
        ]
    );
    $regularUser->syncRoles([$staffRole->id]);

    $this->actingAs($adminUser);

    // Cannot delete self or default user
    expect(UserResource::canDeleteUser($adminUser))->toBeFalse()
        ->and(UserResource::canDeleteUser($regularUser))->toBeTrue();

    // Cannot remove Admin role from the only admin user
    expect(fn () => UserResource::ensureAdminRoleConstraints($adminUser, [$staffRole->id]))
        ->toThrow(ValidationException::class);
});

it('enforces OwnershipScope & Bouncer isolation across Global, Group (Team), and Individual users without stale cache', function (): void {
    $teamAlpha = Team::query()->create(['name' => 'Tim Retail Alpha']);

    $userGlobal = User::query()->create([
        'name'                => 'Manager Global',
        'email'               => 'global.mgr@example.com',
        'password'            => bcrypt('password123'),
        'is_active'           => true,
        'resource_permission' => PermissionType::GLOBAL,
    ]);

    $userTeamA1 = User::query()->create([
        'name'                => 'Leader Alpha 1',
        'email'               => 'alpha1@example.com',
        'password'            => bcrypt('password123'),
        'is_active'           => true,
        'resource_permission' => PermissionType::GROUP,
    ]);
    $userTeamA1->teams()->sync([$teamAlpha->id]);

    $userTeamA2 = User::query()->create([
        'name'                => 'Member Alpha 2',
        'email'               => 'alpha2@example.com',
        'password'            => bcrypt('password123'),
        'is_active'           => true,
        'creator_id'          => $userTeamA1->id,
        'resource_permission' => PermissionType::INDIVIDUAL,
    ]);
    $userTeamA2->teams()->sync([$teamAlpha->id]);

    $userOutsider = User::query()->create([
        'name'                => 'Outsider Staff',
        'email'               => 'outsider@example.com',
        'password'            => bcrypt('password123'),
        'is_active'           => true,
        'creator_id'          => $userGlobal->id,
        'resource_permission' => PermissionType::INDIVIDUAL,
    ]);

    // 1. Individual user only gets their own ID from Bouncer
    $this->actingAs($userOutsider);
    expect(bouncer()->getAuthorizedUserIds())->toBe([$userOutsider->id]);

    // 2. Switching to Group user in the same process returns Team Alpha members (no stale cache!)
    $this->actingAs($userTeamA1);
    $teamAllowedIds = bouncer()->getAuthorizedUserIds();
    expect($teamAllowedIds)->toContain($userTeamA1->id, $userTeamA2->id)
        ->and($teamAllowedIds)->not->toContain($userOutsider->id);

    // 3. Switching to Global user returns null (unrestricted)
    $this->actingAs($userGlobal);
    expect(bouncer()->getAuthorizedUserIds())->toBeNull();
});

it('enforces RBAC policies on POS and AttendanceSite resources for Kasir, HRD, and Staff roles', function (): void {
    $posService = app(PosService::class);
    $posService->ensureDefaultSetup();
    $register = PosRegister::query()->firstOrFail();

    $kasirUser = User::query()->create([
        'name'                => 'Kasir Satu',
        'email'               => 'kasir1@example.com',
        'password'            => bcrypt('password123'),
        'is_active'           => true,
        'resource_permission' => PermissionType::INDIVIDUAL,
    ]);
    $kasirUser->assignRole('Kasir');

    $otherKasirUser = User::query()->create([
        'name'                => 'Kasir Dua',
        'email'               => 'kasir2@example.com',
        'password'            => bcrypt('password123'),
        'is_active'           => true,
        'resource_permission' => PermissionType::INDIVIDUAL,
    ]);
    $otherKasirUser->assignRole('Kasir');

    $hrdUser = User::query()->create([
        'name'                => 'HRD Manager',
        'email'               => 'hrd.mgr@example.com',
        'password'            => bcrypt('password123'),
        'is_active'           => true,
        'resource_permission' => PermissionType::GLOBAL,
    ]);
    $hrdUser->assignRole('HRD');

    $session1 = $posService->openSession($register, 100000, $kasirUser->id, 'Shift Kasir 1');
    $order1 = PosOrder::query()->create([
        'name'            => 'INV-POS-RBAC-001',
        'pos_session_id'  => $session1->id,
        'pos_register_id' => $register->id,
        'cashier_id'      => $kasirUser->id,
        'status'          => PosOrder::STATUS_PAID,
        'subtotal'        => 25000,
        'total_amount'    => 25000,
        'paid_amount'     => 25000,
    ]);

    // Kasir 1 can view/update their own session & order, but NOT delete security users
    expect(Gate::forUser($kasirUser)->allows('view', $session1))->toBeTrue()
        ->and(Gate::forUser($kasirUser)->allows('view', $order1))->toBeTrue()
        ->and(Gate::forUser($kasirUser)->allows('viewAny', User::class))->toBeFalse();

    // Kasir 2 (Individual scope) cannot view or update Kasir 1's session or order
    expect(Gate::forUser($otherKasirUser)->allows('view', $session1))->toBeFalse()
        ->and(Gate::forUser($otherKasirUser)->allows('update', $order1))->toBeFalse();

    // HRD can manage AttendanceSite, while Kasir cannot
    expect(Gate::forUser($hrdUser)->allows('viewAny', AttendanceSite::class))->toBeTrue()
        ->and(Gate::forUser($hrdUser)->allows('create', AttendanceSite::class))->toBeTrue()
        ->and(Gate::forUser($kasirUser)->allows('create', AttendanceSite::class))->toBeFalse();
});
