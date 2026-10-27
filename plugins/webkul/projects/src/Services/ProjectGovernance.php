<?php

namespace Webkul\Project\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Webkul\Security\Models\User;

class ProjectGovernance
{
    public const OPERATION_MANAGER_ROLE = 'Operation Manager';

    public static function isOperationManager(?int $userId): bool
    {
        if (! $userId) {
            return false;
        }

        $user = User::query()->withoutGlobalScopes()->find($userId);

        return $user?->hasRole(self::OPERATION_MANAGER_ROLE) ?? false;
    }

    public static function scopeOperationManagers(Builder $query, mixed $selectedId = null): Builder
    {
        return $query->where(function (Builder $inner) use ($selectedId): void {
            $inner->whereHas('roles', function (Builder $roles): void {
                $roles->where('name', self::OPERATION_MANAGER_ROLE);
            });

            if (filled($selectedId)) {
                $inner->orWhereKey($selectedId);
            }
        });
    }

    public static function assertProjectManager(?int $userId): void
    {
        if (! $userId) {
            throw ValidationException::withMessages([
                'user_id' => __('projects::filament/resources/project.form.sections.additional.fields.project-manager-required'),
            ]);
        }

        if (! self::isOperationManager($userId)) {
            throw ValidationException::withMessages([
                'user_id' => __('projects::filament/resources/project.form.sections.additional.fields.project-manager-must-be-om'),
            ]);
        }
    }

    public static function assertStrategicDirector(bool $isStrategic, mixed $directorId): void
    {
        if ($isStrategic && blank($directorId)) {
            throw ValidationException::withMessages([
                'project_director_id' => __('projects::filament/resources/project.form.sections.additional.fields.project-director-required'),
            ]);
        }
    }
}
