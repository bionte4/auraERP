<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Project\Filament\Widgets\Concerns\AppliesDashboardFilters;
use Webkul\Project\Models\Task;
use Webkul\Project\Models\TaskStage;

class TaskByStageChart extends ChartWidget
{
    use AppliesDashboardFilters;
    use HasWidgetShield, InteractsWithPageFilters;

    protected ?string $heading = 'Tasks By Stage';

    protected ?string $maxHeight = '250px';

    protected static ?int $sort = 1;

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_task_by_stage_chart';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/task-by-stage.heading.title');
    }

    protected function getData(): array
    {
        $datasets = [
            'datasets' => [],
            'labels'   => [],
        ];

        foreach (TaskStage::all() as $stage) {
            if (in_array($stage->name, $datasets['labels'])) {
                $datasets['labels'][] = $stage->name.' '.$stage->id;
            } else {
                $datasets['labels'][] = $stage->name;
            }

            $query = Task::query();
            $this->applyOperationalProjectScope($query);

            $selectedProjects = $this->selectedFilterIds('selectedProjects');
            $selectedAssignees = $this->selectedFilterIds('selectedAssignees');
            $selectedTags = $this->selectedFilterIds('selectedTags');
            $selectedPartners = $this->selectedFilterIds('selectedPartners');

            if ($selectedProjects !== []) {
                $query->whereIn('project_id', $selectedProjects);
            }

            if ($selectedAssignees !== []) {
                $query->whereHas('users', function ($q) use ($selectedAssignees) {
                    $q->whereIn('users.id', $selectedAssignees);
                });
            }

            if ($selectedTags !== []) {
                $query->whereHas('tags', function ($q) use ($selectedTags) {
                    $q->whereIn('projects_task_tag.tag_id', $selectedTags);
                });
            }

            if ($selectedPartners !== []) {
                $query->whereIn('parent_id', $selectedPartners);
            }

            $startDate = ! is_null($this->pageFilters['startDate'] ?? null) ?
                Carbon::parse($this->pageFilters['startDate']) :
                null;

            $endDate = ! is_null($this->pageFilters['endDate'] ?? null) ?
                Carbon::parse($this->pageFilters['endDate']) :
                now();

            $datasets['datasets'][] = $query
                ->whereBetween('created_at', [$startDate, $endDate])
                ->where('stage_id', $stage->id)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => __('projects::filament/widgets/task-by-stage.datasets.label'),
                    'data'  => $datasets['datasets'],
                ],
            ],
            'labels' => $datasets['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
