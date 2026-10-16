<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Task;
use Webkul\Project\Services\OperationalScope;
use Webkul\TimeOff\Enums\State as LeaveState;
use Webkul\TimeOff\Models\Leave;

class OperationManagerActionsWidget extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $pollingInterval = '60s';

    protected int|string|array $columnSpan = 'full';

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_operation_manager_actions_widget';
    }

    protected function getHeading(): ?string
    {
        return __('projects::filament/widgets/operation-manager-actions.heading.title');
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $scope = app(OperationalScope::class);

        return [
            Stat::make(
                __('projects::filament/widgets/operation-manager-actions.stats.pending-leaves'),
                (string) $this->pendingLeavesCount($scope)
            )
                ->description(__('projects::filament/widgets/operation-manager-actions.stats.pending-leaves-help'))
                ->color('warning')
                ->url(url('/admin/time-off/time-offs')),
            Stat::make(
                __('projects::filament/widgets/operation-manager-actions.stats.expiring-contracts'),
                (string) $this->expiringContractsCount($scope)
            )
                ->description(__('projects::filament/widgets/operation-manager-actions.stats.expiring-contracts-help'))
                ->color('danger')
                ->url(url('/admin/employees/employees')),
            Stat::make(
                __('projects::filament/widgets/operation-manager-actions.stats.overdue-tasks'),
                (string) $this->overdueTasksCount($scope)
            )
                ->description(__('projects::filament/widgets/operation-manager-actions.stats.overdue-tasks-help'))
                ->color('danger')
                ->url(url('/admin/project/tasks')),
            Stat::make(
                __('projects::filament/widgets/operation-manager-actions.stats.without-user'),
                (string) $this->assignedWithoutUserCount($scope)
            )
                ->description(__('projects::filament/widgets/operation-manager-actions.stats.without-user-help'))
                ->color('gray')
                ->url(url('/admin/project/assign-employees')),
        ];
    }

    protected function pendingLeavesCount(OperationalScope $scope): int
    {
        try {
            if (! class_exists(Leave::class) || ! Schema::hasTable('time_off_leaves')) {
                return 0;
            }

            $query = Leave::query()->whereIn('state', [LeaveState::CONFIRM->value, LeaveState::VALIDATE_ONE->value]);
            $employeeIds = $scope->employeeIdsInScope();

            if (! $scope->isUnscoped()) {
                if ($employeeIds->isEmpty()) {
                    return 0;
                }

                $query->whereIn('employee_id', $employeeIds->all());
            }

            return $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function expiringContractsCount(OperationalScope $scope): int
    {
        try {
            if (! class_exists(Employee::class) || ! Schema::hasTable('employees_employees')) {
                return 0;
            }

            $query = Employee::query()
                ->whereNotNull('contract_end_date')
                ->whereDate('contract_end_date', '>=', now()->toDateString())
                ->whereDate('contract_end_date', '<=', now()->addDays(30)->toDateString());

            $employeeIds = $scope->employeeIdsInScope();

            if (! $scope->isUnscoped()) {
                if ($employeeIds->isEmpty()) {
                    return 0;
                }

                $query->whereIn('id', $employeeIds->all());
            }

            return $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function overdueTasksCount(OperationalScope $scope): int
    {
        try {
            if (! Schema::hasTable('projects_tasks')) {
                return 0;
            }

            $query = Task::query()
                ->whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value, TaskState::APPROVED->value]);

            if (! $scope->isUnscoped()) {
                $projectIds = $scope->projectIds();

                if ($projectIds === []) {
                    return 0;
                }

                $query->whereIn('project_id', $projectIds);
            }

            return $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function assignedWithoutUserCount(OperationalScope $scope): int
    {
        try {
            if (! Schema::hasTable('projects_project_employees') || ! Schema::hasTable('employees_employees')) {
                return 0;
            }

            $query = Employee::query()->whereNull('user_id')->whereHas('projects');
            $employeeIds = $scope->employeeIdsInScope();

            if (! $scope->isUnscoped()) {
                if ($employeeIds->isEmpty()) {
                    return 0;
                }

                $query->whereIn('id', $employeeIds->all());
            }

            return $query->count();
        } catch (Throwable) {
            return 0;
        }
    }
}
