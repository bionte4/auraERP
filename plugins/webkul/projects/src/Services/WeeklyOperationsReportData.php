<?php

namespace Webkul\Project\Services;

use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Task;
use Webkul\Security\Models\User;
use Webkul\Security\Support\AdminAccess;
use Webkul\TimeOff\Enums\State as LeaveState;
use Webkul\TimeOff\Models\Leave;

class WeeklyOperationsReportData
{
    public function __construct(
        protected OperationalScope $scope,
    ) {}

    /**
     * @return array{
     *     scope_label: string,
     *     week_label: string,
     *     is_unscoped: bool,
     *     metrics: array<int, array{key: string, metric: string, value: int, notes: string, href: string}>,
     *     load_rows: array<int, array{name: string, projects: int, hours: float}>
     * }
     */
    public function build(?User $user = null): array
    {
        $user ??= auth()->user();
        $indonesian = str_starts_with((string) app()->getLocale(), 'id');
        $unscoped = $this->scope->isUnscoped($user);

        return [
            'scope_label' => $unscoped
                ? ($indonesian ? 'Cakupan: seluruh perusahaan' : 'Scope: company-wide')
                : ($indonesian ? 'Cakupan: proyek / tim Anda' : 'Scope: your projects / team'),
            'week_label'  => $indonesian
                ? 'Minggu '.now()->startOfWeek()->translatedFormat('d M').' – '.now()->endOfWeek()->translatedFormat('d M Y')
                : 'Week of '.now()->startOfWeek()->format('M j').' – '.now()->endOfWeek()->format('M j, Y'),
            'is_unscoped' => $unscoped,
            'metrics'     => $this->metrics($indonesian, $user),
            'load_rows'   => $this->loadRows(50, $user),
        ];
    }

    /**
     * @return array<int, array{key: string, metric: string, value: int, notes: string, href: string}>
     */
    public function metrics(bool $indonesian = false, ?User $user = null): array
    {
        $user ??= auth()->user();

        return [
            [
                'key'    => 'pending_leave',
                'metric' => $indonesian ? 'Cuti menunggu persetujuan' : 'Pending leave approvals',
                'value'  => $this->pendingLeaves($user),
                'notes'  => $indonesian ? 'Menunggu konfirmasi / validasi level-1' : 'Waiting confirm / level-1 validation',
                'href'   => url('/admin/time-off/time-offs'),
            ],
            [
                'key'    => 'expiring_contracts',
                'metric' => $indonesian ? 'Kontrak berakhir ≤ 30 hari' : 'Contracts ending ≤ 30 days',
                'value'  => $this->expiringContracts($user),
                'notes'  => $indonesian ? 'Perlu keputusan perpanjang / offboarding' : 'Needs renew or offboarding decision',
                'href'   => url('/admin/employees/employees'),
            ],
            [
                'key'    => 'overdue_tasks',
                'metric' => $indonesian ? 'Tugas terlambat' : 'Overdue tasks',
                'value'  => $this->overdueTasks($user),
                'notes'  => $indonesian ? 'Lewat deadline dan belum selesai' : 'Past deadline and not done',
                'href'   => url('/admin/project/tasks'),
            ],
            [
                'key'    => 'active_projects',
                'metric' => $indonesian ? 'Proyek aktif' : 'Active projects',
                'value'  => $this->activeProjects($user),
                'notes'  => $indonesian ? 'Proyek dengan status aktif pada cakupan Anda' : 'Active projects in your scope',
                'href'   => url('/admin/project/projects'),
            ],
            [
                'key'    => 'without_user',
                'metric' => $indonesian ? 'Ditugaskan tanpa Related User' : 'Assigned without Related User',
                'value'  => $this->withoutUser($user),
                'notes'  => $indonesian ? 'Belum bisa login ke sistem proyek' : 'Cannot log in to project yet',
                'href'   => url('/admin/project/assign-employees'),
            ],
        ];
    }

    /**
     * @return array<int, array{name: string, projects: int, hours: float}>
     */
    public function loadRows(int $limit = 50, ?User $user = null): array
    {
        $user ??= auth()->user();

        if (! class_exists(Employee::class) || ! Schema::hasTable('projects_project_employees')) {
            return [];
        }

        $query = Employee::query()
            ->where('is_active', true)
            ->whereHas('projects');

        if (! $this->scope->isUnscoped($user)) {
            $employeeIds = $this->scope->employeeIdsInScope($user);

            if ($employeeIds->isEmpty()) {
                return [];
            }

            $query->whereIn('id', $employeeIds->all());
        }

        $employees = $query
            ->orderBy('name')
            ->limit(max(10, $limit * 3))
            ->get(['id', 'name']);

        if ($employees->isEmpty()) {
            return [];
        }

        $capacity = app(EmployeeCapacity::class)->forEmployees($employees);

        return $employees
            ->map(fn (Employee $employee): array => [
                'name'     => $employee->name,
                'projects' => $capacity[$employee->id]['projects'] ?? 0,
                'hours'    => $capacity[$employee->id]['hours'] ?? 0.0,
            ])
            ->sortByDesc('hours')
            ->take($limit)
            ->values()
            ->all();
    }

    protected function pendingLeaves(?User $user): int
    {
        try {
            if (! class_exists(Leave::class) || ! Schema::hasTable('time_off_leaves')) {
                return 0;
            }

            $query = Leave::query()->whereIn('state', [LeaveState::CONFIRM->value, LeaveState::VALIDATE_ONE->value]);

            if (! $this->scope->isUnscoped($user)) {
                $employeeIds = $this->scope->employeeIdsInScope($user);

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

    protected function expiringContracts(?User $user): int
    {
        try {
            if (! class_exists(Employee::class) || ! Schema::hasTable('employees_employees')) {
                return 0;
            }

            $query = Employee::query()
                ->whereNotNull('contract_end_date')
                ->whereDate('contract_end_date', '>=', now()->toDateString())
                ->whereDate('contract_end_date', '<=', now()->addDays(30)->toDateString());

            if (! $this->scope->isUnscoped($user)) {
                $employeeIds = $this->scope->employeeIdsInScope($user);

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

    protected function overdueTasks(?User $user): int
    {
        try {
            if (! Schema::hasTable('projects_tasks')) {
                return 0;
            }

            $query = Task::query()
                ->whereNotNull('deadline')
                ->where('deadline', '<', now())
                ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value, TaskState::APPROVED->value]);

            if (! $this->scope->isUnscoped($user)) {
                $projectIds = $this->scope->projectIds($user);

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

    protected function activeProjects(?User $user): int
    {
        try {
            $query = Project::query()->where('is_active', true);

            if (! $this->scope->isUnscoped($user)) {
                $projectIds = $this->scope->projectIds($user);

                if ($projectIds === []) {
                    return 0;
                }

                $query->whereIn('id', $projectIds);
            }

            return $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function withoutUser(?User $user): int
    {
        try {
            if (! class_exists(Employee::class) || ! Schema::hasTable('projects_project_employees')) {
                return 0;
            }

            $query = Employee::query()->whereNull('user_id')->whereHas('projects');

            if (! $this->scope->isUnscoped($user)) {
                $employeeIds = $this->scope->employeeIdsInScope($user);

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

    public function viewerMayAccess(?User $user = null): bool
    {
        $user ??= auth()->user();

        if (! $user instanceof User || ! method_exists($user, 'hasRole')) {
            return false;
        }

        return AdminAccess::check($user)
            || $user->hasRole('HRD')
            || $user->hasRole('Operation Manager');
    }
}
