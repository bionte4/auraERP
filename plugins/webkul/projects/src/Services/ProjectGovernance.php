<?php

namespace Webkul\Project\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\ProjectUnit;
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

    public static function assertStrategicUnits(Project $project): void
    {
        if (! $project->is_strategic) {
            return;
        }

        if ($project->units()->count() < 1) {
            throw ValidationException::withMessages([
                'units' => __('projects::filament/resources/project.form.sections.additional.fields.units-required'),
            ]);
        }
    }

    public static function assertUnitLeadership(int $mosEmployeeId, int $teamLeaderEmployeeId): void
    {
        if ($mosEmployeeId < 1 || $teamLeaderEmployeeId < 1) {
            throw ValidationException::withMessages([
                'mos_employee_id' => __('projects::filament/resources/project.form.sections.additional.fields.unit-roles-required'),
            ]);
        }

        if ($mosEmployeeId === $teamLeaderEmployeeId) {
            throw ValidationException::withMessages([
                'team_leader_employee_id' => __('projects::filament/resources/project.form.sections.additional.fields.unit-mos-not-team-leader'),
            ]);
        }
    }

    public static function assertUnitUniqueness(ProjectUnit $unit): void
    {
        if ($unit->department_id && ProjectUnit::query()
            ->when($unit->exists, fn (Builder $query) => $query->whereKeyNot($unit->getKey()))
            ->where('project_id', $unit->project_id)
            ->where('department_id', $unit->department_id)
            ->exists()
        ) {
            throw ValidationException::withMessages([
                'department_id' => __('projects::filament/resources/project.form.sections.additional.fields.unit-department-unique'),
            ]);
        }

        $seatIds = array_values(array_filter([
            (int) $unit->mos_employee_id,
            (int) $unit->team_leader_employee_id,
        ]));

        if ($seatIds === []) {
            return;
        }

        $taken = ProjectUnit::query()
            ->when($unit->exists, fn (Builder $query) => $query->whereKeyNot($unit->getKey()))
            ->where(function (Builder $query) use ($seatIds): void {
                $query->whereIn('mos_employee_id', $seatIds)
                    ->orWhereIn('team_leader_employee_id', $seatIds);
            })
            ->whereHas('project', fn (Builder $query) => $query->where('is_active', true))
            ->exists();

        if ($taken) {
            throw ValidationException::withMessages([
                'mos_employee_id' => __('projects::filament/resources/project.form.sections.additional.fields.unit-role-unique'),
            ]);
        }
    }
}
