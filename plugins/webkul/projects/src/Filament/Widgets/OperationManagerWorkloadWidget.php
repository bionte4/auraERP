<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;
use Throwable;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Widgets\Concerns\AppliesDashboardFilters;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\User;

class OperationManagerWorkloadWidget extends BaseWidget
{
    use AppliesDashboardFilters;
    use HasWidgetShield;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_operation_manager_workload_widget';
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        $operationManagerCount = $this->operationManagerQuery()->count();

        return __('projects::filament/widgets/operation-manager-workload.heading.title', [
            'count' => $operationManagerCount,
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                $this->operationManagerQuery()
                    ->with(['managedProjects' => fn ($query) => $query->orderBy('name')])
                    ->withCount([
                        'managedProjects as managed_projects_count' => fn (Builder $query) => $query->where('is_active', true),
                    ])
                    ->orderBy('name')
            )
            ->queryStringIdentifier('omWorkload')
            ->defaultPaginationPageOption(10)
            ->paginationPageOptions([5, 10, 25])
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/widgets/operation-manager-workload.table-columns.operation-manager'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('projects::filament/widgets/operation-manager-workload.table-columns.email'))
                    ->toggleable(),
                TextColumn::make('managed_projects_count')
                    ->label(__('projects::filament/widgets/operation-manager-workload.table-columns.project-count'))
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('managed_projects')
                    ->label(__('projects::filament/widgets/operation-manager-workload.table-columns.projects'))
                    ->html()
                    ->state(function (User $record): HtmlString {
                        $projects = $record->managedProjects
                            ->where('is_active', true)
                            ->values();

                        if ($projects->isEmpty()) {
                            return new HtmlString(
                                '<span class="text-gray-400">'.e(__('projects::filament/widgets/operation-manager-workload.table-columns.none')).'</span>'
                            );
                        }

                        $links = $projects->map(function (Project $project): string {
                            $url = $this->projectViewUrl($project);

                            return '<a href="'.e($url).'" class="text-primary-600 hover:underline dark:text-primary-400">'.e($project->name).'</a>';
                        })->implode('<span class="text-gray-400"> · </span>');

                        return new HtmlString($links);
                    }),
            ])
            ->emptyStateHeading(__('projects::filament/widgets/operation-manager-workload.empty.heading'))
            ->emptyStateDescription(__('projects::filament/widgets/operation-manager-workload.empty.description'));
    }

    protected function projectViewUrl(Project $project): string
    {
        try {
            return ProjectResource::getUrl('view', ['record' => $project]);
        } catch (Throwable) {
            return url('/admin/project/projects/'.$project->getRouteKey());
        }
    }

    /**
     * @return Builder<User>
     */
    protected function operationManagerQuery(): Builder
    {
        if (! Schema::hasTable('projects_projects')) {
            return User::query()->whereRaw('1 = 0');
        }

        $query = User::query()
            ->where('is_active', true)
            ->whereHas('roles', fn (Builder $query) => $query->where('name', 'Operation Manager'));

        // Scoped viewers (an OM looking at the dashboard) only see their
        // own workload row; Admin/HRD keep the full overview.
        if ($this->operationalProjectIds() !== null && auth()->id()) {
            $query->whereKey(auth()->id());
        }

        return $query;
    }
}
