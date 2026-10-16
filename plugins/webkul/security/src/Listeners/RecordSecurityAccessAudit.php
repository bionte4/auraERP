<?php

namespace Webkul\Security\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Webkul\Security\Models\User;
use Webkul\Security\Services\LoginProtection;
use Webkul\Security\Services\SecurityAuditLogger;
use Webkul\Security\Support\DemoAccountGuard;

class RecordSecurityAccessAudit
{
    public function __construct(
        protected SecurityAuditLogger $logger,
        protected LoginProtection $loginProtection,
    ) {}

    public function handleLogin(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $this->loginProtection->recordSuccess($event->user->email);

        if (DemoAccountGuard::shouldBlock($event->user)) {
            auth()->guard($event->guard)->logout();

            return;
        }

        $this->logger->logLoginSucceeded($event->user, $event->guard);
    }

    public function handleFailed(Failed $event): void
    {
        $email = is_array($event->credentials)
            ? ($event->credentials['email'] ?? null)
            : null;

        $email = is_string($email) ? $email : null;

        $this->loginProtection->recordFailure($email);

        $this->logger->logLoginFailed($email, $event->guard);
    }

    public function handleLogout(Logout $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $this->logger->logLogout($event->user, $event->guard);
    }
}
