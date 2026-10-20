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
use Webkul\Project\Filament\Widgets\ExpiringProjectsWidget;
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
use Webkul\Support\Filament\Support\SelectedFilterIds;

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

    public function bootedHasFiltersForm(): void
    {
        $this->normalizeNativeFilterValues();
        $this->cacheSchema('filtersForm', $this->getFiltersForm());
    }

    public function getFiltersForm(): Schema
    {
        if ((! $this->isCachingSchemas) && $this->hasCachedSchema('filtersForm')) {
            return $this->getSchema('filtersForm');
        }

        // Do not use wire:partial here — it remorphs the filter row and leaves
        // Filament's Alpine multi-selects as empty, non-clickable boxes.
        $schema = $this->makeSchema()
            ->live(debounce: 500)
            ->statePath('filters');

        return $this->filtersForm($schema);
    }

    public function updatedFilters(): void
    {
        $this->normalizeNativeFilterValues();

        if ($this->persistsFiltersInSession()) {
            session()->put(
                $this->getFiltersSessionKey(),
                $this->filters,
            );
        }
    }

    public function filtersForm(Schema $schema): Schema
    {
        // Native <select> only — Filament disables native mode when multiple/searchable
        // is enabled, and those Alpine selects are failing to initialize on this page.
        $projectOptions = Project::query()->orderBy('name')->pluck('name', 'id')->all();
        $assigneeOptions = User::query()->orderBy('name')->pluck('name', 'id')->all();
        $tagOptions = Tag::query()->orderBy('name')->pluck('name', 'id')->all();
        // Same external-customer filter as Project form — raw Partner::all() includes
        // employee/user partner rows (sub_type employee / linked partner_id).
        // Limit to partners already used as project customers when any exist.
        $projectPartnerIds = Project::query()
            ->whereNotNull('partner_id')
            ->distinct()
            ->pluck('partner_id');

        $partnerQuery = Project::filterExternalCustomersQuery(Partner::query());

        if ($projectPartnerIds->isNotEmpty()) {
            $partnerQuery->whereIn('partners_partners.id', $projectPartnerIds);
        }

        $partnerOptions = $partnerQuery
            ->orderBy('partners_partners.name')
            ->pluck('partners_partners.name', 'partners_partners.id')
            ->all();
        $allLabel = __('projects::filament/pages/dashboard.filters-form.all');

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
                            ->native()
                            ->options($projectOptions)
                            ->placeholder($allLabel),
                        Select::make('selectedAssignees')
                            ->label(__('projects::filament/pages/dashboard.filters-form.assignees'))
                            ->native()
                            ->options($assigneeOptions)
                            ->placeholder($allLabel),
                        Select::make('selectedTags')
                            ->label(__('projects::filament/pages/dashboard.filters-form.tags'))
                            ->native()
                            ->options($tagOptions)
                            ->placeholder($allLabel),
                        Select::make('selectedPartners')
                            ->label(__('projects::filament/pages/dashboard.filters-form.customer'))
                            ->native()
                            ->options($partnerOptions)
                            ->placeholder($allLabel),
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
            ExpiringProjectsWidget::class,
            AttendancePresenceWidget::class,
            OperationManagerWorkloadWidget::class,
            TaskByStageChart::class,
            TaskByStateChart::class,
            TopAssigneesWidget::class,
            TopProjectsWidget::class,
        ];
    }

    /**
     * Widgets still call whereIn(), so expose selected values as list-shaped filters.
     * Native selects store a scalar (or null); older sessions may still have arrays.
     *
     * @return array<int, int|string>
     */
    public static function selectedFilterIds(mixed $value): array
    {
        return SelectedFilterIds::make($value);
    }

    protected function normalizeNativeFilterValues(): void
    {
        if (! is_array($this->filters)) {
            return;
        }

        foreach (['selectedProjects', 'selectedAssignees', 'selectedTags', 'selectedPartners'] as $key) {
            if (! array_key_exists($key, $this->filters)) {
                continue;
            }

            if (is_array($this->filters[$key])) {
                $this->filters[$key] = $this->filters[$key][0] ?? null;
            }

            if ($this->filters[$key] === '' || $this->filters[$key] === false) {
                $this->filters[$key] = null;
            }
        }
    }
}
