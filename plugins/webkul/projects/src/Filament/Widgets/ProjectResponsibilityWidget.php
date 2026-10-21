<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Throwable;
use Webkul\Project\Enums\TaskState;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Widgets\Concerns\AppliesDashboardFilters;
use Webkul\Project\Models\Project;

/**
 * Who is responsible for what: one row per active project with its
 * Project Manager (Operation Manager), Team Leader, member count,
 * task progress, overdue tasks, and time left to the end date.
 * Scoped via OperationalScope — Operation Managers see the projects
 * they manage or join, Team Leaders only the project they lead,
 * Admin/HRD everything. Deliberately shows NO contract value or
 * invoicing data (see OperationalScope::canViewProjectValue()).
 */
class ProjectResponsibilityWidget extends BaseWidget
{
    use AppliesDashboardFilters;
    use HasWidgetShield, InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_project_responsibility_widget';
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/responsibility.heading.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->projectsQuery())
            ->defaultSort('name', 'asc')
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25])
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/widgets/responsibility.table-columns.project'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->url(fn (Project $record): string => $this->projectViewUrl($record)),
                TextColumn::make('partner.name')
                    ->label(__('projects::filament/widgets/responsibility.table-columns.customer'))
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label(__('projects::filament/widgets/responsibility.table-columns.project-manager'))
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('teamLeader.name')
                    ->label(__('projects::filament/widgets/responsibility.table-columns.team-leader'))
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('members_count')
                    ->label(__('projects::filament/widgets/responsibility.table-columns.members'))
                    ->alignCenter(),
                TextColumn::make('task_progress')
                    ->label(__('projects::filament/widgets/responsibility.table-columns.progress'))
                    ->alignCenter()
                    ->state(function (Project $record): string {
                        $total = (int) ($record->total_tasks_count ?? 0);

                        if ($total === 0) {
                            return '—';
                        }

                        $done = (int) ($record->completed_tasks_count ?? 0);

                        return ((int) round(($done / $total) * 100)).'% ('.$done.'/'.$total.')';
                    }),
                TextColumn::make('overdue_tasks_count')
                    ->label(__('projects::filament/widgets/responsibility.table-columns.overdue-tasks'))
                    ->alignCenter()
                    ->badge()
                    ->color(fn (Project $record): string => (int) ($record->overdue_tasks_count ?? 0) > 0 ? 'danger' : 'gray'),
                TextColumn::make('deadline_status')
                    ->label(__('projects::filament/widgets/responsibility.table-columns.deadline'))
                    ->badge()
                    ->state(fn (Project $record): string => $this->deadlineStatusLabel($record))
                    ->color(fn (Project $record): string => $this->deadlineStatusColor($record)),
            ])
            ->recordUrl(fn (Project $record): string => $this->projectViewUrl($record))
            ->emptyStateHeading(__('projects::filament/widgets/responsibility.empty.heading'))
            ->emptyStateDescription(__('projects::filament/widgets/responsibility.empty.description'))
            ->emptyStateIcon('heroicon-o-user-group');
    }

    protected function projectsQuery(): Builder
    {
        $query = Project::query()
            ->with(['partner', 'user', 'teamLeader'])
            ->withCount([
                'employees as members_count',
                'tasks as total_tasks_count',
                'tasks as completed_tasks_count' => fn (Builder $taskQuery) => $taskQuery
                    ->whereIn('state', [TaskState::DONE->value, TaskState::APPROVED->value]),
                'tasks as overdue_tasks_count' => fn (Builder $taskQuery) => $taskQuery
                    ->whereNotNull('deadline')
                    ->where('deadline', '<', now())
                    ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value, TaskState::APPROVED->value]),
            ])
            ->where('is_active', true);

        $this->applyOperationalProjectScope($query, $query->getModel()->getTable().'.id');

        $selectedProjects = $this->selectedFilterIds('selectedProjects');
        $selectedPartners = $this->selectedFilterIds('selectedPartners');

        if ($selectedProjects !== []) {
            $query->whereIn('id', $selectedProjects);
        }

        if ($selectedPartners !== []) {
            $query->whereIn('partner_id', $selectedPartners);
        }

        return $query;
    }

    protected function deadlineStatusLabel(Project $record): string
    {
        $days = $record->daysUntilEndDate();

        if ($days === null) {
            return '—';
        }

        if ($days < 0) {
            return __('projects::filament/widgets/responsibility.status.overdue', ['days' => abs($days)]);
        }

        if ($days === 0) {
            return __('projects::filament/widgets/responsibility.status.ends-today');
        }

        return __('projects::filament/widgets/responsibility.status.days-left', ['days' => $days]);
    }

    protected function deadlineStatusColor(Project $record): string
    {
        $days = $record->daysUntilEndDate();

        return match (true) {
            $days === null => 'gray',
            $days <= 30    => 'danger',
            $days <= 60    => 'warning',
            default        => 'info',
        };
    }

    protected function projectViewUrl(Project $project): string
    {
        try {
            return ProjectResource::getUrl('view', ['record' => $project]);
        } catch (Throwable) {
            return url('/admin/project/projects/'.$project->getRouteKey());
        }
    }
}
