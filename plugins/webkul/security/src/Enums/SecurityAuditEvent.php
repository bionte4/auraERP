<?php

namespace Webkul\Security\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum SecurityAuditEvent: string implements HasColor, HasLabel
{
    case LoginSucceeded = 'login_succeeded';
    case LoginFailed = 'login_failed';
    case Logout = 'logout';
    case UserCreated = 'user_created';
    case UserUpdated = 'user_updated';
    case UserDeleted = 'user_deleted';
    case UserRolesChanged = 'user_roles_changed';
    case RoleCreated = 'role_created';
    case RoleUpdated = 'role_updated';
    case RoleDeleted = 'role_deleted';
    case BackupCreated = 'backup_created';
    case BackupRestored = 'backup_restored';
    case BackupDeleted = 'backup_deleted';

    public function getLabel(): string
    {
        return __('security::enums/security-audit-event.'.$this->value);
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::LoginSucceeded, self::BackupCreated                                          => 'success',
            self::LoginFailed                                                                  => 'danger',
            self::Logout                                                                       => 'gray',
            self::UserCreated, self::RoleCreated                                               => 'info',
            self::UserUpdated, self::UserRolesChanged, self::RoleUpdated, self::BackupRestored => 'warning',
            self::UserDeleted, self::RoleDeleted, self::BackupDeleted                          => 'danger',
        };
    }
}
