<?php

namespace Webkul\Employee\Filament\Widgets\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Filament\Pages\EmployeeDashboard;
use Webkul\Project\Services\OperationalScope;

trait AppliesPeopleDashboardFilters
{
    /**
     * @return array<int, int|string>
     */
    protected function selectedFilterIds(string $key): array
    {
        return EmployeeDashboard::selectedFilterIds($this->pageFilters[$key] ?? null);
    }

    /**
     * Apply the dashboard top filters (department / employment type / work location / company).
     */
    protected function applyPeopleFilters(Builder $query): Builder
    {
        $departments = $this->selectedFilterIds('selectedDepartments');
        $employmentTypes = $this->selectedFilterIds('selectedEmploymentTypes');
        $workLocations = $this->selectedFilterIds('selectedWorkLocations');
        $companies = $this->selectedFilterIds('selectedCompanies');

        if ($departments !== []) {
            $query->whereIn('department_id', $departments);
        }

        if ($employmentTypes !== []) {
            $query->whereIn('employment_type_id', $employmentTypes);
        }

        if ($workLocations !== []) {
            $query->whereIn('work_location_id', $workLocations);
        }

        if ($companies !== []) {
            $query->whereIn('company_id', $companies);
        }

        return $query;
    }

    /**
     * Admin/HRD see everyone; other dashboard viewers (e.g. Operation Manager)
     * only see employees in their operational scope plus themselves and direct reports.
     */
    protected function applyPeopleScope(Builder $query): Builder
    {
        if (! class_exists(OperationalScope::class)) {
            return $query;
        }

        $scope = app(OperationalScope::class);

        if ($scope->isUnscoped()) {
            return $query;
        }

        $employeeIds = $scope->employeeIdsInScope()->all();

        $ownEmployeeId = auth()->user()?->employee?->id;

        if ($ownEmployeeId) {
            $employeeIds[] = (int) $ownEmployeeId;

            if (Schema::hasTable('employees_employees')) {
                $employeeIds = array_merge(
                    $employeeIds,
                    \Webkul\Employee\Models\Employee::query()
                        ->where('parent_id', $ownEmployeeId)
                        ->pluck('id')
                        ->map(fn ($id): int => (int) $id)
                        ->all(),
                );
            }
        }

        $employeeIds = array_values(array_unique(array_map('intval', $employeeIds)));

        if ($employeeIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('id', $employeeIds);
    }

    protected function projectsAvailable(): bool
    {
        return Schema::hasTable('projects_projects')
            && Schema::hasTable('projects_project_employees');
    }

    protected function scopedPeopleQuery(): Builder
    {
        $query = \Webkul\Employee\Models\Employee::query();

        $this->applyPeopleFilters($query);
        $this->applyPeopleScope($query);

        return $query;
    }
}
