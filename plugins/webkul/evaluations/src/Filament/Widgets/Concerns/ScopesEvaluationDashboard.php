<?php

namespace Webkul\Evaluation\Filament\Widgets\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Services\OperationalScope;

/**
 * The evaluation dashboard is an aggregate view: Admin/HRD see every
 * employee, while an Operation Manager only sees evaluations of
 * employees in their operational scope (their project teams), plus
 * evaluations where they or their direct reports act as reviewer or
 * approver in the L1/L2 chain.
 */
trait ScopesEvaluationDashboard
{
    /**
     * @return array{employeeIds: array<int, int>, approverIds: array<int, int>}|null null = unscoped
     */
    protected static function evaluationDashboardScope(): ?array
    {
        if (! class_exists(OperationalScope::class)) {
            return null;
        }

        $scope = app(OperationalScope::class);

        if ($scope->isUnscoped()) {
            return null;
        }

        $employeeIds = $scope->employeeIdsInScope()
            ->map(fn ($id): int => (int) $id)
            ->all();

        $approverIds = [];

        $ownEmployeeId = auth()->user()?->employee?->id;

        if ($ownEmployeeId) {
            $approverIds[] = (int) $ownEmployeeId;

            if (Schema::hasTable('employees_employees')) {
                $approverIds = array_merge(
                    $approverIds,
                    Employee::query()
                        ->where('parent_id', $ownEmployeeId)
                        ->pluck('id')
                        ->map(fn ($id): int => (int) $id)
                        ->all(),
                );
            }
        }

        $approverIds = array_values(array_unique($approverIds));
        $employeeIds = array_values(array_unique(array_merge($employeeIds, $approverIds)));

        return [
            'employeeIds' => $employeeIds,
            'approverIds' => $approverIds,
        ];
    }

    protected function applyEvaluationScope(Builder $query): Builder
    {
        $scope = static::evaluationDashboardScope();

        if ($scope === null) {
            return $query;
        }

        if ($scope['employeeIds'] === [] && $scope['approverIds'] === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $q) use ($scope): void {
            $q->whereIn('employee_id', $scope['employeeIds'] ?: [0])
                ->orWhereIn('reviewer_id', $scope['approverIds'] ?: [0])
                ->orWhereIn('gm_approver_id', $scope['approverIds'] ?: [0])
                ->orWhereIn('director_approver_id', $scope['approverIds'] ?: [0]);
        });
    }
}
