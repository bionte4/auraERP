<?php

namespace Webkul\Support\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Models\Task;
use Webkul\Project\Services\OperationalScope;
use Webkul\Security\Support\AdminAccess;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\TimeOff\Enums\State as LeaveState;
use Webkul\TimeOff\Models\Leave;

class MyDayDashboard extends Page
{
    protected string $view = 'filament.pages.my-day-dashboard';

    protected static ?string $slug = 'my-day';

    protected static ?int $navigationSort = -9;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-sun';

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user && method_exists($user, 'hasRole');
    }

    public static function getNavigationLabel(): string
    {
        return str_starts_with((string) app()->getLocale(), 'id') ? 'Hari Saya' : 'My Day';
    }

    public function getTitle(): string|Htmlable
    {
        return static::getNavigationLabel();
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    /**
     * @return array<string, mixed>
     */
    public function getDayData(): array
    {
        $user = auth()->user();
        $indonesian = str_starts_with((string) app()->getLocale(), 'id');
        $role = $this->primaryRoleLabel($user);
        $scope = app(OperationalScope::class);

        $cards = match (true) {
            AdminAccess::check($user) || (method_exists($user, 'hasRole') && $user->hasRole('HRD')) => $this->hrdAdminCards($indonesian, $scope),
            method_exists($user, 'hasRole') && $user->hasRole('Operation Manager')                  => $this->omCards($indonesian, $scope),
            method_exists($user, 'hasRole') && $user->hasRole('Kasir')                              => $this->kasirCards($indonesian),
            default                                                                                 => $this->staffCards($indonesian, $user),
        };

        return [
            'greeting' => $indonesian
                ? 'Halo, '.($user->name ?? '').' — fokus hari ini'
                : 'Hi, '.($user->name ?? '').' — today\'s focus',
            'role'     => $role,
            'cards'    => $cards,
        ];
    }

    protected function primaryRoleLabel(mixed $user): string
    {
        if (! $user || ! method_exists($user, 'getRoleNames')) {
            return '—';
        }

        return (string) ($user->getRoleNames()->first() ?? '—');
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function hrdAdminCards(bool $indonesian, OperationalScope $scope): array
    {
        return [
            $this->card(
                $indonesian ? 'Cuti menunggu' : 'Pending leave',
                (string) $this->pendingLeavesCount($scope),
                url('/admin/time-off/time-offs'),
                'warning'
            ),
            $this->card(
                $indonesian ? 'Kontrak ≤ 30 hari' : 'Contracts ≤ 30 days',
                (string) $this->expiringContractsCount($scope),
                url('/admin/employees/employees'),
                'danger'
            ),
            $this->card(
                $indonesian ? 'Belum absen hari ini' : 'Not clocked in today',
                (string) $this->notClockedInToday(),
                url('/admin/attendances'),
                'gray'
            ),
            $this->card(
                $indonesian ? 'Tanpa Related User' : 'Without Related User',
                (string) $this->withoutUserCount($scope),
                url('/admin/project/assign-employees'),
                'info'
            ),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function omCards(bool $indonesian, OperationalScope $scope): array
    {
        return [
            $this->card(
                $indonesian ? 'Tugas terlambat (scope)' : 'Overdue tasks (scope)',
                (string) $this->overdueTasksCount($scope),
                url('/admin/project/tasks'),
                'danger'
            ),
            $this->card(
                $indonesian ? 'Cuti menunggu (scope)' : 'Pending leave (scope)',
                (string) $this->pendingLeavesCount($scope),
                url('/admin/time-off/time-offs'),
                'warning'
            ),
            $this->card(
                $indonesian ? 'Proyek saya' : 'My projects',
                (string) count($scope->projectIds()),
                url('/admin/project/projects'),
                'primary'
            ),
            $this->card(
                $indonesian ? 'Tanpa Related User' : 'Without Related User',
                (string) $this->withoutUserCount($scope),
                url('/admin/project/assign-employees'),
                'gray'
            ),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function staffCards(bool $indonesian, mixed $user): array
    {
        $myOpenTasks = 0;
        $myTimesheetHours = 0.0;

        try {
            if ($user && Schema::hasTable('projects_tasks')) {
                $myOpenTasks = Task::query()
                    ->whereHas('users', fn ($q) => $q->where('users.id', $user->id))
                    ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value, TaskState::APPROVED->value])
                    ->count();
            }

            if ($user && Schema::hasTable('analytic_records')) {
                $myTimesheetHours = (float) DB::table('analytic_records')
                    ->where('user_id', $user->id)
                    ->whereDate('date', now()->toDateString())
                    ->sum('unit_amount');
            }
        } catch (Throwable) {
        }

        return [
            $this->card(
                $indonesian ? 'Tugas terbuka saya' : 'My open tasks',
                (string) $myOpenTasks,
                url('/admin/project/tasks'),
                'primary'
            ),
            $this->card(
                $indonesian ? 'Jam timesheet hari ini' : 'Timesheet hours today',
                number_format($myTimesheetHours, 1),
                url('/admin/timesheets'),
                'success'
            ),
            $this->card(
                $indonesian ? 'Absensi' : 'Attendance',
                $indonesian ? 'Catat kehadiran' : 'Clock in/out',
                url('/admin/absen'),
                'info'
            ),
            $this->card(
                $indonesian ? 'Cuti saya' : 'My time off',
                '→',
                url('/admin/time-off/my-time-offs'),
                'gray'
            ),
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function kasirCards(bool $indonesian): array
    {
        return [
            $this->card(
                $indonesian ? 'POS Terminal' : 'POS Terminal',
                '→',
                url('/admin/pos-terminal'),
                'primary'
            ),
            $this->card(
                $indonesian ? 'Absensi' : 'Attendance',
                '→',
                url('/admin/absen'),
                'info'
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function card(string $label, string $value, string $href, string $color): array
    {
        return compact('label', 'value', 'href', 'color');
    }

    protected function pendingLeavesCount(OperationalScope $scope): int
    {
        try {
            if (! class_exists(Leave::class) || ! Schema::hasTable('time_off_leaves')) {
                return 0;
            }

            $query = Leave::query()->whereIn('state', [LeaveState::CONFIRM->value, LeaveState::VALIDATE_ONE->value]);
            $employeeIds = $scope->employeeIdsInScope();

            if (! $scope->isUnscoped() && $employeeIds->isNotEmpty()) {
                $query->whereIn('employee_id', $employeeIds->all());
            } elseif (! $scope->isUnscoped()) {
                return 0;
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

            if (! $scope->isUnscoped() && $employeeIds->isNotEmpty()) {
                $query->whereIn('id', $employeeIds->all());
            } elseif (! $scope->isUnscoped()) {
                return 0;
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

            $projectIds = $scope->projectIds();

            if (! $scope->isUnscoped()) {
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

    protected function withoutUserCount(OperationalScope $scope): int
    {
        try {
            if (! Schema::hasTable('projects_project_employees')) {
                return 0;
            }

            $query = Employee::query()->whereNull('user_id')->whereHas('projects');
            $employeeIds = $scope->employeeIdsInScope();

            if (! $scope->isUnscoped() && $employeeIds->isNotEmpty()) {
                $query->whereIn('id', $employeeIds->all());
            } elseif (! $scope->isUnscoped()) {
                return 0;
            }

            return $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function notClockedInToday(): int
    {
        try {
            if (! Schema::hasTable('attendances_attendances') || ! Schema::hasTable('employees_employees')) {
                return 0;
            }

            $active = Employee::query()->where('is_active', true)->count();
            $clocked = DB::table('attendances_attendances')
                ->whereDate('check_in', now()->toDateString())
                ->distinct('employee_id')
                ->count('employee_id');

            return max(0, $active - $clocked);
        } catch (Throwable) {
            return 0;
        }
    }
}
