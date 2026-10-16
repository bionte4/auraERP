<?php

namespace Webkul\Security\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Security\Enums\SecurityAuditEvent;
use Webkul\Security\Models\AuditTrail;
use Webkul\Security\Models\User;

class SecurityAuditLogger
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function log(
        SecurityAuditEvent $event,
        string $description,
        ?Model $actor = null,
        ?Model $subject = null,
        array $properties = [],
        ?string $guard = null,
        ?string $actorLabel = null,
        ?string $subjectLabel = null,
    ): void {
        try {
            if (! Schema::hasTable('security_audit_trails')) {
                return;
            }

            $actor ??= Auth::user();

            AuditTrail::query()->create([
                'event'         => $event,
                'description'   => $description,
                'actor_type'    => $actor?->getMorphClass(),
                'actor_id'      => $actor?->getKey(),
                'actor_label'   => $actorLabel ?? $this->labelFor($actor),
                'subject_type'  => $subject?->getMorphClass(),
                'subject_id'    => $subject?->getKey(),
                'subject_label' => $subjectLabel ?? $this->labelFor($subject),
                'ip_address'    => Request::ip(),
                'user_agent'    => Request::userAgent(),
                'guard'         => $guard,
                'properties'    => $properties === [] ? null : $properties,
                'created_at'    => now(),
            ]);
        } catch (Throwable) {
            // Never break the primary request because of audit logging.
        }
    }

    public function logLoginSucceeded(User $user, ?string $guard = null): void
    {
        $this->log(
            SecurityAuditEvent::LoginSucceeded,
            __('security::audit-trail.descriptions.login-succeeded', ['email' => $user->email]),
            actor: $user,
            subject: $user,
            guard: $guard,
        );
    }

    public function logLoginFailed(?string $email, ?string $guard = null): void
    {
        $this->log(
            SecurityAuditEvent::LoginFailed,
            __('security::audit-trail.descriptions.login-failed', ['email' => $email ?: '—']),
            actor: null,
            actorLabel: $email,
            properties: ['email' => $email],
            guard: $guard,
        );
    }

    public function logLogout(User $user, ?string $guard = null): void
    {
        $this->log(
            SecurityAuditEvent::Logout,
            __('security::audit-trail.descriptions.logout', ['email' => $user->email]),
            actor: $user,
            subject: $user,
            guard: $guard,
        );
    }

    /**
     * @param  array<int, string>  $roleNames
     */
    public function logUserRolesChanged(User $user, array $roleNames, ?User $actor = null): void
    {
        $this->log(
            SecurityAuditEvent::UserRolesChanged,
            __('security::audit-trail.descriptions.user-roles-changed', [
                'email' => $user->email,
                'roles' => $roleNames === [] ? '—' : implode(', ', $roleNames),
            ]),
            actor: $actor,
            subject: $user,
            properties: ['roles' => array_values($roleNames)],
        );
    }

    protected function labelFor(?Model $model): ?string
    {
        if (! $model) {
            return null;
        }

        if ($model instanceof User) {
            return $model->email ?: $model->name;
        }

        foreach (['name', 'email', 'title'] as $attribute) {
            $value = $model->getAttribute($attribute);

            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return (string) $model->getKey();
    }
}
