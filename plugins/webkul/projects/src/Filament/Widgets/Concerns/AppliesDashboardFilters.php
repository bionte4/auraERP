<?php

namespace Webkul\Project\Filament\Widgets\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Webkul\Project\Filament\Pages\Dashboard;
use Webkul\Project\Services\OperationalScope;

trait AppliesDashboardFilters
{
    /**
     * @return array<int, int|string>
     */
    protected function selectedFilterIds(string $key): array
    {
        return Dashboard::selectedFilterIds($this->pageFilters[$key] ?? null);
    }

    /**
     * Project IDs the current user may see on operational dashboards,
     * or null when the user is unscoped (Admin/HRD: no restriction).
     * Scoped users (OM, Team Leader, Staff) only see projects assigned
     * to them — managed, member, or led projects respectively.
     *
     * @return array<int, int>|null
     */
    protected function operationalProjectIds(): ?array
    {
        $scope = app(OperationalScope::class);

        if ($scope->isUnscoped()) {
            return null;
        }

        return $scope->projectIds();
    }

    protected function applyOperationalProjectScope(Builder $query, string $column = 'project_id'): Builder
    {
        $projectIds = $this->operationalProjectIds();

        if ($projectIds === null) {
            return $query;
        }

        return $projectIds === []
            ? $query->whereRaw('1 = 0')
            : $query->whereIn($column, $projectIds);
    }
}
