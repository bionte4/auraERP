<?php

namespace Webkul\Security\Listeners;

use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;

class RecordSecurityAccessAudit
{
    public function __construct(protected SecurityAuditLogger $logger) {}

    public function handleLogin(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $this->logger->logLoginSucceeded($event->user, $event->guard);
    }

    public function handleFailed(Failed $event): void
    {
        $email = is_array($event->credentials)
            ? ($event->credentials['email'] ?? null)
            : null;

        $this->logger->logLoginFailed(
            is_string($email) ? $email : null,
            $event->guard,
        );
    }

    public function handleLogout(Logout $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $this->logger->logLogout($event->user, $event->guard);
    }
}
