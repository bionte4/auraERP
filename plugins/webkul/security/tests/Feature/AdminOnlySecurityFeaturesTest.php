<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Webkul\Security\Enums\SecurityAuditEvent;
use Webkul\Security\Filament\Pages\ManageBlockedIps;
use Webkul\Security\Filament\Pages\ManageDatabaseBackups;
use Webkul\Security\Filament\Resources\AuditTrailResource;
use Webkul\Security\Models\AuditTrail;
use Webkul\Security\Models\User;
use Webkul\Security\Policies\AuditTrailPolicy;
use Webkul\Security\Services\RolePermissionSynchronizer;
use Webkul\Security\Support\AdminAccess;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    SecurityHelper::disableUserEvents();

    if (! Schema::hasTable('security_audit_trails')) {
        $migration = include __DIR__.'/../../database/migrations/2026_10_16_160000_create_security_audit_trails_table.php';
        $migration->up();
    }
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

/**
 * @return array<int, string>
 */
function ensureRolesForGuards(array $roleNames): array
{
    foreach ($roleNames as $roleName) {
        foreach (['web', 'sanctum'] as $guard) {
            Role::query()->firstOrCreate([
                'name'       => $roleName,
                'guard_name' => $guard,
            ]);
        }
    }

    return $roleNames;
}

it('keeps audit trail and database backup permissions admin-only after rbac sync', function (): void {
    app(RolePermissionSynchronizer::class)->syncAll(false);

    $adminOnly = AdminAccess::adminOnlyPermissionNames();

    foreach ($adminOnly as $permission) {
        expect(Permission::query()->where('name', $permission)->exists())->toBeTrue();
    }

    $admin = Role::query()->where('name', AdminAccess::adminRoleName())->firstOrFail();

    foreach ($adminOnly as $permission) {
        expect($admin->hasPermissionTo($permission))->toBeTrue();
    }

    foreach (['HRD', 'Operation Manager', 'Kasir', 'Staff'] as $roleName) {
        $role = Role::query()->where('name', $roleName)->firstOrFail();

        // Even if previously granted, sync must revoke admin-only perms.
        $role->givePermissionTo($adminOnly);

        app(RolePermissionSynchronizer::class)->syncAll(false);
        $role->refresh();

        foreach ($adminOnly as $permission) {
            expect($role->hasPermissionTo($permission))->toBeFalse(
                "Role {$roleName} must not keep {$permission}"
            );
        }
    }
});

it('blocks non-admin roles from audit trail resource and policy crud', function (): void {
    $adminRoleName = AdminAccess::adminRoleName();
    ensureRolesForGuards([$adminRoleName, 'HRD', 'Operation Manager', 'Kasir', 'Staff']);

    $trail = AuditTrail::query()->create([
        'event'       => SecurityAuditEvent::LoginSucceeded,
        'description' => 'rbac audit check',
        'created_at'  => now(),
    ]);

    Gate::policy(AuditTrail::class, AuditTrailPolicy::class);

    foreach (['HRD', 'Operation Manager', 'Kasir', 'Staff'] as $roleName) {
        $user = User::withoutEvents(fn () => User::factory()->create());
        $user->syncRoles([$roleName]);

        Auth::guard('web')->login($user);
        Auth::guard('sanctum')->setUser($user);
        Auth::shouldUse('sanctum');

        expect(AdminAccess::check($user))->toBeFalse()
            ->and(AuditTrailResource::canAccess())->toBeFalse()
            ->and(AuditTrailResource::canViewAny())->toBeFalse()
            ->and(AuditTrailResource::canView($trail))->toBeFalse()
            ->and(AuditTrailResource::canCreate())->toBeFalse()
            ->and(AuditTrailResource::canEdit($trail))->toBeFalse()
            ->and(AuditTrailResource::canDelete($trail))->toBeFalse()
            ->and(Gate::forUser($user)->allows('viewAny', AuditTrail::class))->toBeFalse()
            ->and(Gate::forUser($user)->allows('view', $trail))->toBeFalse()
            ->and(Gate::forUser($user)->allows('create', AuditTrail::class))->toBeFalse()
            ->and(Gate::forUser($user)->allows('update', $trail))->toBeFalse()
            ->and(Gate::forUser($user)->allows('delete', $trail))->toBeFalse();
    }
});

it('allows admin view-only on audit trail and denies all mutations', function (): void {
    $adminRoleName = AdminAccess::adminRoleName();
    ensureRolesForGuards([$adminRoleName]);

    $admin = FilamentHelper::actingAs();
    $admin->syncRoles([$adminRoleName]);

    $trail = AuditTrail::query()->create([
        'event'       => SecurityAuditEvent::Logout,
        'description' => 'admin view check',
        'created_at'  => now(),
    ]);

    Gate::policy(AuditTrail::class, AuditTrailPolicy::class);

    expect(AuditTrailResource::canAccess())->toBeTrue()
        ->and(AuditTrailResource::canViewAny())->toBeTrue()
        ->and(AuditTrailResource::canView($trail))->toBeTrue()
        ->and(AuditTrailResource::canCreate())->toBeFalse()
        ->and(AuditTrailResource::canEdit($trail))->toBeFalse()
        ->and(AuditTrailResource::canDelete($trail))->toBeFalse()
        ->and(AuditTrailResource::canForceDelete($trail))->toBeFalse()
        ->and(AuditTrailResource::canRestore($trail))->toBeFalse()
        ->and(Gate::forUser($admin)->allows('viewAny', AuditTrail::class))->toBeTrue()
        ->and(Gate::forUser($admin)->allows('create', AuditTrail::class))->toBeFalse()
        ->and(Gate::forUser($admin)->allows('update', $trail))->toBeFalse()
        ->and(Gate::forUser($admin)->allows('delete', $trail))->toBeFalse();

    Livewire::test(AuditTrailResource\Pages\ListAuditTrails::class)
        ->assertOk();
});

it('blocks non-admin from database backups page and download route', function (): void {
    $adminRoleName = AdminAccess::adminRoleName();
    ensureRolesForGuards([$adminRoleName, 'Staff', 'HRD', 'Operation Manager']);

    foreach (['Staff', 'HRD', 'Operation Manager'] as $roleName) {
        $user = User::withoutEvents(fn () => User::factory()->create());
        $user->syncRoles([$roleName]);

        Auth::guard('web')->login($user);
        Auth::guard('sanctum')->setUser($user);
        Auth::shouldUse('sanctum');

        expect(ManageDatabaseBackups::canAccess())->toBeFalse()
            ->and(ManageDatabaseBackups::shouldRegisterNavigation())->toBeFalse();

        $this->get(route('security.database-backups.download', [
            'filename' => 'missing.sql.gz',
        ]))->assertForbidden();
    }

    $admin = FilamentHelper::actingAs();
    $admin->syncRoles([$adminRoleName]);

    expect(ManageDatabaseBackups::canAccess())->toBeTrue();

    Livewire::test(ManageDatabaseBackups::class)
        ->assertOk();
});

it('blocks non-admin from blocked ips page', function (): void {
    $adminRoleName = AdminAccess::adminRoleName();
    ensureRolesForGuards([$adminRoleName, 'Staff', 'HRD']);

    foreach (['Staff', 'HRD'] as $roleName) {
        $user = User::withoutEvents(fn () => User::factory()->create());
        $user->syncRoles([$roleName]);

        Auth::guard('web')->login($user);
        Auth::guard('sanctum')->setUser($user);
        Auth::shouldUse('sanctum');

        expect(ManageBlockedIps::canAccess())->toBeFalse()
            ->and(ManageBlockedIps::shouldRegisterNavigation())->toBeFalse();
    }

    $admin = FilamentHelper::actingAs();
    $admin->syncRoles([$adminRoleName]);

    expect(ManageBlockedIps::canAccess())->toBeTrue()
        ->and(ManageBlockedIps::shouldRegisterNavigation())->toBeTrue();

    Livewire::test(ManageBlockedIps::class)
        ->assertOk();
});
