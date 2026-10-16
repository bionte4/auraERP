<?php

namespace Webkul\Project\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\TimeOff\Enums\State as LeaveState;
use Webkul\TimeOff\Models\Leave;

class WeeklyOperationsExport implements FromCollection, WithHeadings, WithTitle
{
    public function title(): string
    {
        return 'Weekly Ops';
    }

    public function headings(): array
    {
        return [
            'Metric',
            'Value',
            'Notes',
        ];
    }

    public function collection(): Collection
    {
        $rows = collect([
            ['Pending leave approvals', $this->pendingLeaves(), 'confirm / validate_one'],
            ['Contracts ending ≤ 30 days', $this->expiringContracts(), 'employee contract_end_date'],
            ['Overdue tasks', $this->overdueTasks(), 'deadline passed, not done'],
            ['Active projects', $this->activeProjects(), 'is_active = true'],
            ['Assigned employees without Related User', $this->withoutUser(), 'cannot login'],
        ]);

        return $rows;
    }

    protected function pendingLeaves(): int
    {
        try {
            if (! class_exists(Leave::class) || ! Schema::hasTable('time_off_leaves')) {
                return 0;
            }

            return Leave::query()
                ->whereIn('state', [LeaveState::CONFIRM->value, LeaveState::VALIDATE_ONE->value])
                ->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function expiringContracts(): int
    {
        try {
            if (! class_exists(Employee::class) || ! Schema::hasTable('employees_employees')) {
                return 0;
            }

            return Employee::query()
                ->whereNotNull('contract_end_date')
                ->whereDate('contract_end_date', '>=', now()->toDateString())
                ->whereDate('contract_end_date', '<=', now()->addDays(30)->toDateString())
                ->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function overdueTasks(): int
    {
        try {
            if (! Schema::hasTable('projects_tasks')) {
                return 0;
            }

            return Task::query()
                ->whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value, TaskState::APPROVED->value])
                ->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function activeProjects(): int
    {
        try {
            return Project::query()->where('is_active', true)->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function withoutUser(): int
    {
        try {
            if (! class_exists(Employee::class) || ! Schema::hasTable('projects_project_employees')) {
                return 0;
            }

            return Employee::query()
                ->whereNull('user_id')
                ->whereHas('projects')
                ->count();
        } catch (Throwable) {
            return 0;
        }
    }
}
