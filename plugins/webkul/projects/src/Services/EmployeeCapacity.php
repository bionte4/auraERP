<?php

namespace Webkul\Project\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Models\Employee;

class EmployeeCapacity
{
    /**
     * @param  Collection<int, Employee>|array<int, int>  $employeesOrIds
     * @return array<int, array{projects: int, hours: float, label: string}>
     */
    public function forEmployees(Collection|array $employeesOrIds): array
    {
        $ids = $employeesOrIds instanceof Collection
            ? $employeesOrIds->map(fn ($e): int => (int) (is_object($e) ? $e->id : $e))->all()
            : array_map('intval', $employeesOrIds);

        $ids = array_values(array_unique(array_filter($ids)));

        if ($ids === []) {
            return [];
        }

        $projectCounts = [];

        if (Schema::hasTable('projects_project_employees')) {
            $projectCounts = DB::table('projects_project_employees')
                ->whereIn('employee_id', $ids)
                ->selectRaw('employee_id, count(*) as total')
                ->groupBy('employee_id')
                ->pluck('total', 'employee_id')
                ->all();
        }

        $hours = $this->weeklyHoursByEmployee($ids);
        $result = [];

        foreach ($ids as $id) {
            $projectCount = (int) ($projectCounts[$id] ?? 0);
            $weekHours = round((float) ($hours[$id] ?? 0), 1);
            $result[$id] = [
                'projects' => $projectCount,
                'hours'    => $weekHours,
                'label'    => "{$projectCount} proyek · {$weekHours} jam/minggu",
            ];
        }

        return $result;
    }

    public function labelFor(Employee $employee): string
    {
        $data = $this->forEmployees([$employee->id])[$employee->id] ?? null;

        return $data['label'] ?? '0 proyek · 0 jam/minggu';
    }

    /**
     * @param  array<int, int>  $employeeIds
     * @return array<int, float>
     */
    protected function weeklyHoursByEmployee(array $employeeIds): array
    {
        if (! Schema::hasTable('analytic_records') || ! Schema::hasTable('employees_employees')) {
            return [];
        }

        $userMap = Employee::query()
            ->whereIn('id', $employeeIds)
            ->whereNotNull('user_id')
            ->pluck('user_id', 'id')
            ->all();

        if ($userMap === []) {
            return [];
        }

        $userIds = array_values(array_unique(array_map('intval', $userMap)));
        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        $hoursByUser = DB::table('analytic_records')
            ->whereIn('user_id', $userIds)
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('user_id, coalesce(sum(unit_amount), 0) as hours')
            ->groupBy('user_id')
            ->pluck('hours', 'user_id')
            ->all();

        $result = [];

        foreach ($userMap as $employeeId => $userId) {
            $result[(int) $employeeId] = (float) ($hoursByUser[$userId] ?? 0);
        }

        return $result;
    }
}
