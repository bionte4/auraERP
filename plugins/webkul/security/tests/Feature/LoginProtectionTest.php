<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Webkul\Security\Enums\SecurityAuditEvent;
use Webkul\Security\Filament\Auth\Login;
use Webkul\Security\Filament\Pages\ManageBlockedIps;
use Webkul\Security\Models\AuditTrail;
use Webkul\Security\Models\BlockedIp;
use Webkul\Security\Models\User;
use Webkul\Security\Services\LoginProtection;
use Webkul\Security\Support\AdminAccess;

require_once __DIR__.'/../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function (): void {
    SecurityHelper::disableUserEvents();

    config([
        'security-login.enabled'                 => true,
        'security-login.whitelist'               => [],
        'security-login.max_attempts'            => 3,
        'security-login.decay_minutes'           => 1,
        'security-login.block_after_failures'    => 5,
        'security-login.failure_window_minutes'  => 15,
        'security-login.block_minutes'           => 30,
    ]);

    $this->attackerIp = '203.0.113.50';
    $this->withServerVariables(['REMOTE_ADDR' => $this->attackerIp]);

    Cache::flush();
    RateLimiter::clear(app(LoginProtection::class)->rateLimitKey('attacker@example.com', $this->attackerIp));
});

afterEach(function (): void {
    SecurityHelper::restoreUserEvents();
});

it('rate limits repeated failed login attempts for the same email and ip', function (): void {
    expect(Schema::hasTable('security_blocked_ips'))->toBeTrue();

    $protection = app(LoginProtection::class);

    $protection->recordFailure('attacker@example.com', $this->attackerIp);
    $protection->recordFailure('attacker@example.com', $this->attackerIp);
    $protection->recordFailure('attacker@example.com', $this->attackerIp);

    expect($protection->tooManyAttempts('attacker@example.com', $this->attackerIp))->toBeTrue();
});

it('blocks an ip after repeated failures and clears on unblock', function (): void {
    $protection = app(LoginProtection::class);

    for ($i = 0; $i < 5; $i++) {
        $protection->recordFailure('attacker@example.com', $this->attackerIp);
    }

    expect($protection->isBlocked($this->attackerIp))->toBeTrue();

    expect(fn () => $protection->assertNotBlocked($this->attackerIp))
        ->toThrow(RuntimeException::class);

    expect(AuditTrail::query()->where('event', SecurityAuditEvent::IpBlocked)->count())->toBeGreaterThanOrEqual(1);

    $protection->unblock($this->attackerIp);

    expect($protection->isBlocked($this->attackerIp))->toBeFalse()
        ->and(AuditTrail::query()->where('event', SecurityAuditEvent::IpUnblocked)->count())->toBeGreaterThanOrEqual(1);
});

it('never blocks whitelisted ips', function (): void {
    config(['security-login.whitelist' => [$this->attackerIp]]);

    $protection = app(LoginProtection::class);

    for ($i = 0; $i < 10; $i++) {
        $protection->recordFailure('attacker@example.com', $this->attackerIp);
    }

    expect($protection->isBlocked($this->attackerIp))->toBeFalse()
        ->and($protection->isWhitelisted($this->attackerIp))->toBeTrue();
});

it('rejects filament login when the client ip is blocked', function (): void {
    $protection = Mockery::mock(LoginProtection::class)->makePartial();
    $protection->shouldReceive('assertNotBlocked')
        ->once()
        ->andThrow(new RuntimeException(__('security::login-protection.errors.ip-blocked', ['minutes' => 30])));
    $protection->shouldReceive('tooManyAttempts')->andReturn(false);
    app()->instance(LoginProtection::class, $protection);

    User::withoutEvents(fn () => User::factory()->create([
        'email'     => 'victim@example.com',
        'password'  => Hash::make('password'),
        'is_active' => true,
    ]));

    Livewire::test(Login::class)
        ->fillForm([
            'email'    => 'victim@example.com',
            'password' => 'password',
        ])
        ->call('authenticate')
        ->assertHasFormErrors(['email']);
});

it('rejects api login when rate limited or ip blocked', function (): void {
    $protection = app(LoginProtection::class);

    for ($i = 0; $i < 3; $i++) {
        $protection->recordFailure('api@example.com', $this->attackerIp);
    }

    $this->postJson('/admin/api/v1/login', [
        'email'    => 'api@example.com',
        'password' => 'wrong-password',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['email']);

    $protection->clearRateLimit('api@example.com', $this->attackerIp);
    $protection->block($this->attackerIp, 30);

    $this->postJson('/admin/api/v1/login', [
        'email'    => 'api@example.com',
        'password' => 'wrong-password',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('allows admin to unblock an ip from the manage page', function (): void {
    $adminRoleName = AdminAccess::adminRoleName();

    foreach (['web', 'sanctum'] as $guard) {
        Role::query()->firstOrCreate([
            'name'       => $adminRoleName,
            'guard_name' => $guard,
        ]);
    }

    BlockedIp::query()->create([
        'ip_address'      => $this->attackerIp,
        'failed_attempts' => 12,
        'reason'          => 'login_failures',
        'last_email'      => 'attacker@example.com',
        'blocked_until'   => now()->addMinutes(30),
    ]);

    $admin = FilamentHelper::actingAs();
    $admin->syncRoles([$adminRoleName]);

    Livewire::test(ManageBlockedIps::class)
        ->assertOk()
        ->call('unblockIp', $this->attackerIp)
        ->assertNotified();

    expect(app(LoginProtection::class)->isBlocked($this->attackerIp))->toBeFalse();
});
