<?php

namespace Webkul\Employee\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Employee\Filament\Widgets\ExpiringContractsWidget;
use Webkul\Employee\Filament\Widgets\PeopleAttentionWidget;
use Webkul\Employee\Filament\Widgets\PeopleStatsWidget;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\EmploymentType;
use Webkul\Employee\Models\WorkLocation;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Support\SelectedFilterIds;
use Webkul\Support\Models\Company;

class EmployeeDashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;

    protected static string $routePath = 'employees/dashboard';

    protected static ?int $navigationSort = -8;

    protected static function getPagePermission(): ?string
    {
        return 'page_employee_people_dashboard';
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/pages/people-dashboard.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-user-group';
    }

    public function getTitle(): string|Htmlable
    {
        return __('employees::filament/pages/people-dashboard.navigation.title');
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
        // Native <select> only — Alpine multi/searchable selects have failed to init on dashboards.
        $departmentOptions = Department::query()->orderBy('name')->pluck('name', 'id')->all();
        $employmentTypeOptions = EmploymentType::query()->orderBy('name')->pluck('name', 'id')->all();
        $workLocationOptions = WorkLocation::query()->orderBy('name')->pluck('name', 'id')->all();
        $companyOptions = Company::query()->orderBy('name')->pluck('name', 'id')->all();
        $allLabel = __('employees::filament/pages/people-dashboard.filters-form.all');

        return $schema
            ->components([
                Section::make()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'xl'      => 4,
                    ])
                    ->schema([
                        Select::make('selectedDepartments')
                            ->label(__('employees::filament/pages/people-dashboard.filters-form.department'))
                            ->native()
                            ->options($departmentOptions)
                            ->placeholder($allLabel),
                        Select::make('selectedEmploymentTypes')
                            ->label(__('employees::filament/pages/people-dashboard.filters-form.employment-type'))
                            ->native()
                            ->options($employmentTypeOptions)
                            ->placeholder($allLabel),
                        Select::make('selectedWorkLocations')
                            ->label(__('employees::filament/pages/people-dashboard.filters-form.work-location'))
                            ->native()
                            ->options($workLocationOptions)
                            ->placeholder($allLabel),
                        Select::make('selectedCompanies')
                            ->label(__('employees::filament/pages/people-dashboard.filters-form.company'))
                            ->native()
                            ->options($companyOptions)
                            ->placeholder($allLabel),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function getWidgets(): array
    {
        return [
            PeopleStatsWidget::class,
            ExpiringContractsWidget::class,
            PeopleAttentionWidget::class,
        ];
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

        foreach (['selectedDepartments', 'selectedEmploymentTypes', 'selectedWorkLocations', 'selectedCompanies'] as $key) {
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
