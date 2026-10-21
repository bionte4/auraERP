<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
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

class ExpiringProjectsWidget extends BaseWidget
{
    use AppliesDashboardFilters;
    use HasWidgetShield, InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_expiring_projects_widget';
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/expiring-projects.heading.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->expiringProjectsQuery())
            ->queryStringIdentifier('expiringProjects')
            ->defaultSort('end_date', 'asc')
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25])
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/widgets/expiring-projects.table-columns.project'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->url(fn (Project $record): string => $this->projectViewUrl($record)),
                TextColumn::make('partner.name')
                    ->label(__('projects::filament/widgets/expiring-projects.table-columns.customer'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('user.name')
                    ->label(__('projects::filament/widgets/expiring-projects.table-columns.project-manager'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('end_date')
                    ->label(__('projects::filament/widgets/expiring-projects.table-columns.end-date'))
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('deadline_status')
                    ->label(__('projects::filament/widgets/expiring-projects.table-columns.status'))
                    ->badge()
                    ->state(fn (Project $record): string => $this->deadlineStatusLabel($record))
                    ->color(fn (Project $record): string => $this->deadlineStatusColor($record)),
                TextColumn::make('task_progress')
                    ->label(__('projects::filament/widgets/expiring-projects.table-columns.progress'))
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
                    ->label(__('projects::filament/widgets/expiring-projects.table-columns.overdue-tasks'))
                    ->alignCenter()
                    ->badge()
                    ->color(fn (Project $record): string => (int) ($record->overdue_tasks_count ?? 0) > 0 ? 'danger' : 'gray'),
            ])
            ->filters([
                SelectFilter::make('deadline_window')
                    ->label(__('projects::filament/widgets/expiring-projects.filters.window.label'))
                    ->options([
                        'expired'   => __('projects::filament/widgets/expiring-projects.filters.window.expired'),
                        'within_30' => __('projects::filament/widgets/expiring-projects.filters.window.within-30'),
                        '31_60'     => __('projects::filament/widgets/expiring-projects.filters.window.31-60'),
                        '61_90'     => __('projects::filament/widgets/expiring-projects.filters.window.61-90'),
                    ])
                    ->placeholder(__('projects::filament/widgets/expiring-projects.filters.window.all'))
                    ->query(function (Builder $query, array $data): Builder {
                        $today = now()->startOfDay()->toDateString();

                        return match ($data['value'] ?? null) {
                            'expired'   => $query->whereDate('end_date', '<', $today),
                            'within_30' => $query->whereDate('end_date', '>=', $today)
                                ->whereDate('end_date', '<=', now()->addDays(30)->toDateString()),
                            '31_60'     => $query->whereDate('end_date', '>', now()->addDays(30)->toDateString())
                                ->whereDate('end_date', '<=', now()->addDays(60)->toDateString()),
                            '61_90'     => $query->whereDate('end_date', '>', now()->addDays(60)->toDateString())
                                ->whereDate('end_date', '<=', now()->addDays(90)->toDateString()),
                            default     => $query,
                        };
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Project $record): string => $this->projectViewUrl($record)),
                EditAction::make()
                    ->url(fn (Project $record): string => $this->projectEditUrl($record)),
            ])
            ->recordUrl(fn (Project $record): string => $this->projectViewUrl($record))
            ->emptyStateHeading(__('projects::filament/widgets/expiring-projects.empty.heading'))
            ->emptyStateDescription(__('projects::filament/widgets/expiring-projects.empty.description'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    /**
     * Active projects already past end_date, or ending within the 3-month reminder window.
     * Respects the dashboard Project / Customer filters (not the timesheet date range).
     */
    protected function expiringProjectsQuery(): Builder
    {
        $cutoffDate = max(
            now()->addDays(Project::REMINDER_WINDOW_DAYS)->toDateString(),
            now()->addMonths(3)->toDateString(),
        );

        $query = Project::query()
            ->with(['partner', 'user'])
            ->withCount([
                'tasks as total_tasks_count',
                'tasks as completed_tasks_count' => fn (Builder $taskQuery) => $taskQuery
                    ->whereIn('state', [TaskState::DONE->value, TaskState::APPROVED->value]),
                'tasks as overdue_tasks_count' => fn (Builder $taskQuery) => $taskQuery
                    ->whereNotNull('deadline')
                    ->where('deadline', '<', now())
                    ->whereNotIn('state', [TaskState::DONE->value, TaskState::CANCELLED->value, TaskState::APPROVED->value]),
            ])
            ->where('is_active', true)
            ->whereNotNull('end_date')
            ->whereDate('end_date', '<=', $cutoffDate);

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
            return __('projects::filament/widgets/expiring-projects.status.overdue', ['days' => abs($days)]);
        }

        if ($days === 0) {
            return __('projects::filament/widgets/expiring-projects.status.ends-today');
        }

        return __('projects::filament/widgets/expiring-projects.status.days-left', ['days' => $days]);
    }

    protected function deadlineStatusColor(Project $record): string
    {
        $days = $record->daysUntilEndDate();

        return match (true) {
            $days === null     => 'gray',
            $days <= 30        => 'danger',
            $days <= 60        => 'warning',
            default            => 'info',
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

    protected function projectEditUrl(Project $project): string
    {
        try {
            return ProjectResource::getUrl('edit', ['record' => $project]);
        } catch (Throwable) {
            return url('/admin/project/projects/'.$project->getRouteKey().'/edit');
        }
    }
}
