<?php

namespace Webkul\Project\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Filament\Widgets\AttendancePresenceWidget;
use Webkul\Project\Filament\Widgets\OperationManagerActionsWidget;
use Webkul\Project\Filament\Widgets\OperationManagerWorkloadWidget;
use Webkul\Project\Filament\Widgets\StatsOverviewWidget;
use Webkul\Project\Filament\Widgets\TaskByStageChart;
use Webkul\Project\Filament\Widgets\TaskByStateChart;
use Webkul\Project\Filament\Widgets\TopAssigneesWidget;
use Webkul\Project\Filament\Widgets\TopProjectsWidget;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\Tag;
use Webkul\Security\Models\User;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Forms\Components\DashboardDateRange;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;

    protected static string $routePath = 'project';

    protected static function getPagePermission(): ?string
    {
        return 'page_project_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/pages/dashboard.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-briefcase';
    }

    public function getFiltersForm(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('filtersForm')) {
            return $this->getSchema('filtersForm');
        }

        // Debounce live updates so choosing a filter option does not re-render
        // every widget on each click (which made the Select feel "dead").
        $schema = $this->makeSchema()
            ->extraAttributes(['wire:partial' => 'table-filters-form'])
            ->live(debounce: 500)
            ->statePath('filters');

        return $this->filtersForm($schema);
    }

    public function filtersForm(Schema $schema): Schema
    {
        // Bake options as plain arrays (no Closure / preload).
        // Closure + preload marks the Select as "dynamic", so opening it fires Livewire
        // and re-renders every dashboard widget — the dropdown never becomes usable.
        $projectOptions = Project::query()->orderBy('name')->pluck('name', 'id')->all();
        $assigneeOptions = User::query()->orderBy('name')->pluck('name', 'id')->all();
        $tagOptions = Tag::query()->orderBy('name')->pluck('name', 'id')->all();
        $partnerOptions = Partner::query()->orderBy('name')->pluck('name', 'id')->all();

        return $schema
            ->components([
                Section::make()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'md'      => 3,
                        'xl'      => 5,
                    ])
                    ->schema([
                        Select::make('selectedProjects')
                            ->label(__('projects::filament/pages/dashboard.filters-form.project'))
                            ->multiple()
                            ->searchable()
                            ->options($projectOptions)
                            ->dynamicOptions(false),
                        Select::make('selectedAssignees')
                            ->label(__('projects::filament/pages/dashboard.filters-form.assignees'))
                            ->multiple()
                            ->searchable()
                            ->options($assigneeOptions)
                            ->dynamicOptions(false),
                        Select::make('selectedTags')
                            ->label(__('projects::filament/pages/dashboard.filters-form.tags'))
                            ->multiple()
                            ->searchable()
                            ->options($tagOptions)
                            ->dynamicOptions(false),
                        Select::make('selectedPartners')
                            ->label(__('projects::filament/pages/dashboard.filters-form.customer'))
                            ->multiple()
                            ->searchable()
                            ->options($partnerOptions)
                            ->dynamicOptions(false),
                        ...DashboardDateRange::make(
                            __('projects::filament/pages/dashboard.filters-form.date-range'),
                        ),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            StatsOverviewWidget::class,
            OperationManagerActionsWidget::class,
            AttendancePresenceWidget::class,
            OperationManagerWorkloadWidget::class,
            TaskByStageChart::class,
            TaskByStateChart::class,
            TopAssigneesWidget::class,
            TopProjectsWidget::class,
        ];
    }
}
