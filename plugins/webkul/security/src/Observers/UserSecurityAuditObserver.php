<?php

namespace Webkul\Security\Observers;

use Webkul\Security\Enums\SecurityAuditEvent;
use Webkul\Security\Models\User;
use Webkul\Security\Services\SecurityAuditLogger;

class UserSecurityAuditObserver
{
    public function __construct(protected SecurityAuditLogger $logger) {}

    public function created(User $user): void
    {
        $this->logger->log(
            SecurityAuditEvent::UserCreated,
            __('security::audit-trail.descriptions.user-created', ['email' => $user->email]),
            subject: $user,
            properties: [
                'email'     => $user->email,
                'is_active' => (bool) $user->is_active,
            ],
        );
    }

    public function updated(User $user): void
    {
        $tracked = ['email', 'is_active', 'password', 'resource_permission'];
        $changes = collect($user->getChanges())
            ->only($tracked)
            ->except(['updated_at'])
            ->keys()
            ->values()
            ->all();

        if ($changes === []) {
            return;
        }

        $safeChanges = array_values(array_diff($changes, ['password']));
        $properties = [
            'changed' => $safeChanges,
        ];

        if (in_array('password', $changes, true)) {
            $properties['password_changed'] = true;
        }

        if (array_key_exists('email', $user->getChanges())) {
            $properties['email'] = $user->email;
        }

        if (array_key_exists('is_active', $user->getChanges())) {
            $properties['is_active'] = (bool) $user->is_active;
        }

        $this->logger->log(
            SecurityAuditEvent::UserUpdated,
            __('security::audit-trail.descriptions.user-updated', ['email' => $user->email]),
            subject: $user,
            properties: $properties,
        );
    }

    public function deleted(User $user): void
    {
        $this->logger->log(
            SecurityAuditEvent::UserDeleted,
            __('security::audit-trail.descriptions.user-deleted', ['email' => $user->email]),
            subject: $user,
            properties: ['email' => $user->email],
        );
    }
}
