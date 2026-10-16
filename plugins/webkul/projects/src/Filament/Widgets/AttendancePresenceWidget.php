<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Attendance\Enums\AttendanceStatus;
use Webkul\Attendance\Filament\Resources\AttendanceResource;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Services\OperationalScope;

class AttendancePresenceWidget extends BaseWidget
{
    use HasWidgetShield;

    protected ?string $pollingInterval = '120s';

    protected int|string|array $columnSpan = 'full';

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_attendance_presence_widget';
    }

    protected function getHeading(): ?string
    {
        return __('projects::filament/widgets/attendance-presence.heading.title');
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $summary = $this->todaySummary();

        $attendanceUrl = $this->attendanceIndexUrl();

        return [
            Stat::make(
                __('projects::filament/widgets/attendance-presence.stats.present'),
                (string) $summary['present']
            )->color('success')->url($attendanceUrl),
            Stat::make(
                __('projects::filament/widgets/attendance-presence.stats.late'),
                (string) $summary['late']
            )->color('warning')->url($attendanceUrl),
            Stat::make(
                __('projects::filament/widgets/attendance-presence.stats.not-in'),
                (string) $summary['not_in']
            )->color('danger')->url($attendanceUrl),
            Stat::make(
                __('projects::filament/widgets/attendance-presence.stats.active'),
                (string) $summary['active']
            )->color('gray')->url(url('/admin/employees/employees')),
        ];
    }

    protected function attendanceIndexUrl(): string
    {
        try {
            if (class_exists(AttendanceResource::class)) {
                return AttendanceResource::getUrl('index');
            }
        } catch (Throwable) {
        }

        return url('/admin/attendances');
    }

    /**
     * @return array{present: int, late: int, not_in: int, active: int}
     */
    protected function todaySummary(): array
    {
        $empty = ['present' => 0, 'late' => 0, 'not_in' => 0, 'active' => 0];

        try {
            if (! Schema::hasTable('attendances_attendances') || ! Schema::hasTable('employees_employees')) {
                return $empty;
            }

            $scope = app(OperationalScope::class);
            $employeeQuery = Employee::query()->where('is_active', true);
            $employeeIds = $scope->employeeIdsInScope();

            if (! $scope->isUnscoped()) {
                if ($employeeIds->isEmpty()) {
                    return $empty;
                }

                $employeeQuery->whereIn('id', $employeeIds->all());
            }

            $activeIds = $employeeQuery->pluck('id')->map(fn ($id): int => (int) $id)->all();
            $active = count($activeIds);

            if ($active === 0) {
                return $empty;
            }

            $today = now()->toDateString();
            $rows = DB::table('attendances_attendances')
                ->whereIn('employee_id', $activeIds)
                ->whereDate('check_in', $today)
                ->select('employee_id', 'status')
                ->get();

            $present = 0;
            $late = 0;
            $clocked = [];

            foreach ($rows as $row) {
                $clocked[(int) $row->employee_id] = true;
                $status = (string) ($row->status ?? '');

                if ($status === AttendanceStatus::Late->value || $status === 'late') {
                    $late++;
                } else {
                    $present++;
                }
            }

            return [
                'present' => $present,
                'late'    => $late,
                'not_in'  => max(0, $active - count($clocked)),
                'active'  => $active,
            ];
        } catch (Throwable) {
            return $empty;
        }
    }
}
