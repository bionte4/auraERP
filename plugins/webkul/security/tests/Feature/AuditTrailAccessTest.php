<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Security\Enums\SecurityAuditEvent;
use Webkul\Security\Filament\Resources\AuditTrailResource;
use Webkul\Security\Models\AuditTrail;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;

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

it('allows only admin to access the audit trail resource', function (): void {
    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate(['name' => $adminRoleName, 'guard_name' => $guard]);
        Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => $guard]);
    }

    $admin = FilamentHelper::actingAs();
    $admin->syncRoles([$adminRoleName]);

    expect(AuditTrailResource::canAccess())->toBeTrue()
        ->and(AuditTrailResource::canViewAny())->toBeTrue()
        ->and(AuditTrailResource::shouldRegisterNavigation())->toBeTrue();

    Livewire::test(AuditTrailResource\Pages\ListAuditTrails::class)
        ->assertOk();

    $staff = User::withoutEvents(fn () => User::factory()->create());
    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate(['name' => 'Staff', 'guard_name' => $guard]);
    }
    $staff->syncRoles(['Staff']);

    Auth::guard('web')->login($staff);
    Auth::guard('sanctum')->setUser($staff);
    Auth::shouldUse('sanctum');

    expect(AuditTrailResource::canAccess())->toBeFalse()
        ->and(AuditTrailResource::canViewAny())->toBeFalse()
        ->and(AuditTrailResource::shouldRegisterNavigation())->toBeFalse();
});

it('records login succeeded and failed attempts in the audit trail', function (): void {
    $user = User::withoutEvents(fn () => User::factory()->create([
        'email' => 'audit.login@example.com',
    ]));

    app(SecurityAuditLogger::class)->logLoginSucceeded($user, 'web');
    app(SecurityAuditLogger::class)->logLoginFailed('wrong@example.com', 'web');

    expect(AuditTrail::query()->where('event', SecurityAuditEvent::LoginSucceeded)->count())->toBe(1)
        ->and(AuditTrail::query()->where('event', SecurityAuditEvent::LoginFailed)->count())->toBe(1)
        ->and(AuditTrail::query()->where('event', SecurityAuditEvent::LoginSucceeded)->value('actor_label'))
        ->toBe('audit.login@example.com');
});

it('forbids create update and delete on audit trail records', function (): void {
    $adminRoleName = config('filament-shield.panel_user.name', 'Admin');

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate(['name' => $adminRoleName, 'guard_name' => $guard]);
    }

    $admin = FilamentHelper::actingAs();
    $admin->syncRoles([$adminRoleName]);

    $trail = AuditTrail::query()->create([
        'event'       => SecurityAuditEvent::Logout,
        'description' => 'test',
        'created_at'  => now(),
    ]);

    expect(AuditTrailResource::canCreate())->toBeFalse()
        ->and(AuditTrailResource::canEdit($trail))->toBeFalse()
        ->and(AuditTrailResource::canDelete($trail))->toBeFalse();
});
