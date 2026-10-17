<?php

namespace Webkul\Project\Filament\Widgets\Concerns;

use Webkul\Project\Filament\Pages\Dashboard;

trait AppliesDashboardFilters
{
    /**
     * @return array<int, int|string>
     */
    protected function selectedFilterIds(string $key): array
    {
        return Dashboard::selectedFilterIds($this->pageFilters[$key] ?? null);
    }
}
