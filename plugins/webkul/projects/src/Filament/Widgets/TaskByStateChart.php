<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Exception;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Carbon;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Widgets\Concerns\AppliesDashboardFilters;
use Webkul\Project\Models\Task;

class TaskByStateChart extends ChartWidget
{
    use AppliesDashboardFilters;
    use HasWidgetShield, InteractsWithPageFilters;

    protected ?string $heading = 'Tasks By State';

    protected ?string $maxHeight = '250px';

    protected static ?int $sort = 1;

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_task_by_state_chart';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/task-by-state.heading.title');
    }

    protected function getData(): array
    {
        $datasets = [
            'datasets' => [],
            'labels'   => [],
        ];

        foreach (TaskState::cases() as $state) {
            $query = Task::query();

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

            $this->applyDateFilters($query);

            $datasets['labels'][] = TaskState::options()[$state->value];
            $datasets['datasets'][] = $query->where('state', $state->value)->count();
        }

        $colors = TaskState::colors();

        return [
            'datasets' => [
                [
                    'data'            => $datasets['datasets'],
                    'backgroundColor' => array_map(
                        fn ($state) => match ($colors[$state] ?? 'gray') {
                            'gray'    => '#a1a1aa',
                            'warning' => '#fbbf24',
                            'success' => '#22c55e',
                            'danger'  => '#ef4444',
                            'primary' => '#3b82f6',
                            default   => '#cccccc',
                        },
                        array_keys(TaskState::options())
                    ),
                ],
            ],
            'labels' => $datasets['labels'],
        ];
    }

    private function applyDateFilters($query): void
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        if (! empty($startDate)) {
            try {
                $startDateCarbon = Carbon::parse($startDate)->startOfDay();
                $query->where('created_at', '>=', $startDateCarbon);
            } catch (Exception) {
            }
        }

        if (! empty($endDate)) {
            try {
                $endDateCarbon = Carbon::parse($endDate)->endOfDay();
                $query->where('created_at', '<=', $endDateCarbon);
            } catch (Exception) {
            }
        }

        if (empty($startDate) && empty($endDate)) {
            $query->whereBetween('created_at', [
                now()->subMonth()->startOfDay(),
                now()->endOfDay(),
            ]);
        }
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
