<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Webkul\Project\Filament\Widgets\Concerns\AppliesDashboardFilters;
use Webkul\Project\Models\Timesheet;

class TopProjectsWidget extends BaseWidget
{
    use AppliesDashboardFilters;
    use HasWidgetShield, InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '15s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_top_projects_widget';
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/top-projects.heading.title');
    }

    public function getTableRecordKey(Model|array $record): string
    {
        return (string) $record['project_id'];
    }

    public function table(Table $table): Table
    {
        $query = Timesheet::query();
        $this->applyOperationalProjectScope($query);

        $selectedProjects = $this->selectedFilterIds('selectedProjects');
        $selectedAssignees = $this->selectedFilterIds('selectedAssignees');
        $selectedTags = $this->selectedFilterIds('selectedTags');
        $selectedPartners = $this->selectedFilterIds('selectedPartners');

        if ($selectedProjects !== []) {
            $query->whereIn('project_id', $selectedProjects);
        }

        if ($selectedAssignees !== []) {
            $query->whereIn('analytic_records.user_id', $selectedAssignees);
        }

        if ($selectedTags !== []) {
            $query->whereHas('project.tags', function ($q) use ($selectedTags) {
                $q->whereIn('projects_project_tag.tag_id', $selectedTags);
            });
        }

        if ($selectedPartners !== []) {
            $query->whereIn('analytic_records.partner_id', $selectedPartners);
        }

        $startDate = ! is_null($this->pageFilters['startDate'] ?? null) ?
            Carbon::parse($this->pageFilters['startDate']) :
            null;

        $endDate = ! is_null($this->pageFilters['endDate'] ?? null) ?
            Carbon::parse($this->pageFilters['endDate']) :
            now();

        $query = $query
            ->join('projects_projects', 'projects_projects.id', '=', 'analytic_records.project_id')
            ->selectRaw('
                analytic_records.project_id,
                projects_projects.name as project_name,
                SUM(analytic_records.unit_amount) as total_hours,
                COUNT(DISTINCT analytic_records.task_id) as total_tasks
            ')
            ->whereBetween('analytic_records.created_at', [$startDate, $endDate])
            ->groupBy('analytic_records.project_id', 'projects_projects.name')
            ->orderByRaw('SUM(analytic_records.unit_amount) DESC')
            ->limit(10);

        return $table
            ->query($query)
            ->queryStringIdentifier('topProjects')
            ->defaultKeySort(false)
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('project_name')
                    ->label(__('projects::filament/widgets/top-projects.table-columns.project-name'))
                    ->sortable(),
                TextColumn::make('total_hours')
                    ->label(__('projects::filament/widgets/top-projects.table-columns.hours-spent'))
                    ->sortable(),
                TextColumn::make('total_tasks')
                    ->label(__('projects::filament/widgets/top-projects.table-columns.tasks'))
                    ->sortable(),
            ]);
    }
}
