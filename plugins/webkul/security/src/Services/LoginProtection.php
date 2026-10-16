<?php

namespace Webkul\Security\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use RuntimeException;
use Throwable;
use Webkul\Security\Enums\SecurityAuditEvent;
use Webkul\Security\Models\BlockedIp;

class LoginProtection
{
    public function __construct(protected SecurityAuditLogger $auditLogger) {}

    public function enabled(): bool
    {
        return (bool) config('security-login.enabled', true);
    }

    public function ip(?string $ip = null): string
    {
        return $ip ?: (string) (Request::ip() ?: '0.0.0.0');
    }

    public function isWhitelisted(?string $ip = null): bool
    {
        $ip = $this->ip($ip);
        $whitelist = config('security-login.whitelist', []);

        return in_array($ip, $whitelist, true);
    }

    public function isBlocked(?string $ip = null): bool
    {
        if (! $this->enabled() || $this->isWhitelisted($ip)) {
            return false;
        }

        $ip = $this->ip($ip);

        try {
            if (! Schema::hasTable('security_blocked_ips')) {
                return false;
            }

            $record = BlockedIp::query()->where('ip_address', $ip)->first();

            return $record instanceof BlockedIp && $record->isCurrentlyBlocked();
        } catch (Throwable) {
            return false;
        }
    }

    public function assertNotBlocked(?string $ip = null): void
    {
        if (! $this->isBlocked($ip)) {
            return;
        }

        $until = $this->blockedUntil($ip);
        $minutes = $until instanceof Carbon
            ? max(1, (int) now()->diffInMinutes($until, false))
            : max(1, (int) config('security-login.block_minutes', 30));

        throw new RuntimeException(__('security::login-protection.errors.ip-blocked', [
            'minutes' => $minutes,
        ]));
    }

    public function blockedUntil(?string $ip = null): ?Carbon
    {
        $ip = $this->ip($ip);

        try {
            if (! Schema::hasTable('security_blocked_ips')) {
                return null;
            }

            $record = BlockedIp::query()->where('ip_address', $ip)->first();

            return $record?->blocked_until;
        } catch (Throwable) {
            return null;
        }
    }

    public function rateLimitKey(?string $email = null, ?string $ip = null): string
    {
        $email = mb_strtolower(trim((string) $email));
        $ip = $this->ip($ip);

        return 'security-login:'.sha1($email.'|'.$ip);
    }

    public function tooManyAttempts(?string $email = null, ?string $ip = null): bool
    {
        if (! $this->enabled()) {
            return false;
        }

        return RateLimiter::tooManyAttempts(
            $this->rateLimitKey($email, $ip),
            max(1, (int) config('security-login.max_attempts', 5))
        );
    }

    public function hitRateLimit(?string $email = null, ?string $ip = null): void
    {
        if (! $this->enabled()) {
            return;
        }

        RateLimiter::hit(
            $this->rateLimitKey($email, $ip),
            max(1, (int) config('security-login.decay_minutes', 1)) * 60
        );
    }

    public function availableIn(?string $email = null, ?string $ip = null): int
    {
        return RateLimiter::availableIn($this->rateLimitKey($email, $ip));
    }

    public function clearRateLimit(?string $email = null, ?string $ip = null): void
    {
        RateLimiter::clear($this->rateLimitKey($email, $ip));
    }

    public function recordFailure(?string $email = null, ?string $ip = null): void
    {
        if (! $this->enabled() || $this->isWhitelisted($ip)) {
            return;
        }

        $ip = $this->ip($ip);
        $email = $email ? mb_strtolower(trim($email)) : null;

        $this->hitRateLimit($email, $ip);

        try {
            if (! Schema::hasTable('security_blocked_ips')) {
                return;
            }

            $windowKey = 'security-login-failures:'.$ip;
            $windowSeconds = max(1, (int) config('security-login.failure_window_minutes', 15)) * 60;
            $failures = (int) Cache::get($windowKey, 0) + 1;
            Cache::put($windowKey, $failures, $windowSeconds);

            $record = BlockedIp::query()->firstOrNew(['ip_address' => $ip]);
            $record->failed_attempts = max((int) $record->failed_attempts, $failures);
            $record->last_email = $email;
            $record->reason = $record->reason ?: 'login_failures';
            $record->save();

            $threshold = max(1, (int) config('security-login.block_after_failures', 10));

            if ($failures >= $threshold && ! $record->isCurrentlyBlocked()) {
                $this->block($ip, null, 'login_failures', $email);
            }
        } catch (Throwable) {
        }
    }

    public function recordSuccess(?string $email = null, ?string $ip = null): void
    {
        if (! $this->enabled()) {
            return;
        }

        $ip = $this->ip($ip);
        $this->clearRateLimit($email, $ip);
        Cache::forget('security-login-failures:'.$ip);

        try {
            if (! Schema::hasTable('security_blocked_ips')) {
                return;
            }

            $record = BlockedIp::query()->where('ip_address', $ip)->first();

            if ($record instanceof BlockedIp && ! $record->isCurrentlyBlocked()) {
                $record->forceFill([
                    'failed_attempts' => 0,
                    'last_email'      => $email ? mb_strtolower(trim($email)) : $record->last_email,
                ])->save();
            }
        } catch (Throwable) {
        }
    }

    public function block(
        string $ip,
        ?int $minutes = null,
        string $reason = 'manual',
        ?string $email = null,
    ): BlockedIp {
        $minutes ??= max(1, (int) config('security-login.block_minutes', 30));
        $until = now()->addMinutes($minutes);

        $record = BlockedIp::query()->firstOrNew(['ip_address' => $ip]);
        $record->blocked_until = $until;
        $record->reason = $reason;
        $record->last_email = $email ?: $record->last_email;
        $record->failed_attempts = max(1, (int) $record->failed_attempts);
        $record->save();

        $this->auditLogger->log(
            SecurityAuditEvent::IpBlocked,
            __('security::login-protection.audit.blocked', [
                'ip'      => $ip,
                'minutes' => $minutes,
            ]),
            properties: [
                'ip'      => $ip,
                'minutes' => $minutes,
                'reason'  => $reason,
                'email'   => $email,
            ],
        );

        return $record->refresh();
    }

    public function unblock(string $ip): void
    {
        Cache::forget('security-login-failures:'.$ip);

        try {
            if (Schema::hasTable('security_blocked_ips')) {
                $record = BlockedIp::query()->where('ip_address', $ip)->first();

                if ($record) {
                    $record->forceFill([
                        'blocked_until'   => null,
                        'failed_attempts' => 0,
                        'reason'          => null,
                    ])->save();
                }
            }
        } catch (Throwable) {
        }

        $this->auditLogger->log(
            SecurityAuditEvent::IpUnblocked,
            __('security::login-protection.audit.unblocked', ['ip' => $ip]),
            properties: ['ip' => $ip],
        );
    }
}
