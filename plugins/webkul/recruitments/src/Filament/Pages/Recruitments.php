<?php

namespace Webkul\Recruitment\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\View\LegacyComponents\Widget;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\EmployeeJobPosition;
use Webkul\Recruitment\Filament\Widgets\ApplicantChartWidget;
use Webkul\Recruitment\Filament\Widgets\JobPositionStatsWidget;
use Webkul\Recruitment\Models\Stage;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Forms\Components\DashboardDateRange;
use Webkul\Support\Filament\Support\SelectedFilterIds;
use Webkul\Support\Models\Company;

class Recruitments extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;

    protected static string $routePath = 'recruitment';

    protected static function getPagePermission(): ?string
    {
        return 'page_recruitment_recruitments';
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/pages/recruitment.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-user-plus';
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
        $allLabel = __('recruitments::filament/pages/recruitment.filters-form.status.options.all');

        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Select::make('selectedJobs')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.job-position'))
                            ->native()
                            ->options(fn () => EmployeeJobPosition::where('is_active', true)->pluck('name', 'id'))
                            ->placeholder($allLabel),
                        Select::make('selectedDepartments')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.departments'))
                            ->native()
                            ->options(fn () => Department::pluck('name', 'id'))
                            ->placeholder($allLabel),
                        Select::make('selectedCompanies')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.companies'))
                            ->native()
                            ->options(fn () => Company::pluck('name', 'id'))
                            ->placeholder($allLabel),
                        Select::make('selectedStages')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.stages'))
                            ->native()
                            ->options(fn () => Stage::pluck('name', 'id'))
                            ->placeholder($allLabel),
                        Select::make('status')
                            ->label(__('recruitments::filament/pages/recruitment.filters-form.status.title'))
                            ->native()
                            ->options([
                                'all'      => __('recruitments::filament/pages/recruitment.filters-form.status.options.all'),
                                'ongoing'  => __('recruitments::filament/pages/recruitment.filters-form.status.options.ongoing'),
                                'hired'    => __('recruitments::filament/pages/recruitment.filters-form.status.options.hired'),
                                'refused'  => __('recruitments::filament/pages/recruitment.filters-form.status.options.refused'),
                                'archived' => __('recruitments::filament/pages/recruitment.filters-form.status.options.archived'),
                            ])
                            ->default('all'),
                        ...DashboardDateRange::make(
                            __('recruitments::filament/pages/recruitment.filters-form.date-range'),
                        ),
                    ])
                    ->columnSpanFull()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'md'      => 3,
                        'xl'      => 7,
                    ]),
            ]);
    }

    /**
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

        foreach (['selectedJobs', 'selectedDepartments', 'selectedCompanies', 'selectedStages'] as $key) {
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

    /**
     * @return array<class-string<Widget> | WidgetConfiguration>
     */
    public function getWidgets(): array
    {
        return [
            JobPositionStatsWidget::class,
            ApplicantChartWidget::class,
        ];
    }
}
