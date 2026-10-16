<?php

namespace Webkul\Security\Observers;

use Webkul\Security\Enums\SecurityAuditEvent;
use Webkul\Security\Models\Role;
use Webkul\Security\Services\SecurityAuditLogger;

class RoleSecurityAuditObserver
{
    public function __construct(protected SecurityAuditLogger $logger) {}

    public function created(Role $role): void
    {
        $this->logger->log(
            SecurityAuditEvent::RoleCreated,
            __('security::audit-trail.descriptions.role-created', ['name' => $role->name]),
            subject: $role,
            properties: [
                'name'       => $role->name,
                'guard_name' => $role->guard_name,
            ],
        );
    }

    public function updated(Role $role): void
    {
        $changes = collect($role->getChanges())
            ->except(['updated_at'])
            ->keys()
            ->values()
            ->all();

        if ($changes === []) {
            return;
        }

        $this->logger->log(
            SecurityAuditEvent::RoleUpdated,
            __('security::audit-trail.descriptions.role-updated', ['name' => $role->name]),
            subject: $role,
            properties: [
                'name'    => $role->name,
                'changed' => $changes,
            ],
        );
    }

    public function deleted(Role $role): void
    {
        $this->logger->log(
            SecurityAuditEvent::RoleDeleted,
            __('security::audit-trail.descriptions.role-deleted', ['name' => $role->name]),
            subject: $role,
            properties: ['name' => $role->name],
        );
    }
}
