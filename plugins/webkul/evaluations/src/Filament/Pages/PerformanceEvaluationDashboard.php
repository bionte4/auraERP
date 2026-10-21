<?php

namespace Webkul\Evaluation\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Enums\EvaluationType;
use Webkul\Evaluation\Exports\EvaluationsMonitoringExport;
use Webkul\Evaluation\Filament\Widgets\Concerns\ScopesEvaluationDashboard;
use Webkul\Evaluation\Filament\Widgets\EvaluationApprovalProgressWidget;
use Webkul\Evaluation\Filament\Widgets\EvaluationByTypeChart;
use Webkul\Evaluation\Filament\Widgets\EvaluationRecommendationChart;
use Webkul\Evaluation\Filament\Widgets\EvaluationStatsOverviewWidget;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Security\Support\AdminAccess;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Support\SelectedFilterIds;

class PerformanceEvaluationDashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;
    use ScopesEvaluationDashboard;

    protected static string $routePath = 'performance-evaluation-dashboard';

    protected static ?int $navigationSort = 3;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download_excel')
                ->label('Download Excel (.xlsx)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (): BinaryFileResponse {
                    $query = Evaluation::query()
                        ->with(['employee.department', 'employee.employmentType', 'reviewer', 'gmApprover', 'ratings'])
                        ->latest('updated_at');

                    if ($ids = SelectedFilterIds::make($this->filters['evaluation_type'] ?? null)) {
                        $query->whereIn('evaluation_type', $ids);
                    }

                    if ($ids = SelectedFilterIds::make($this->filters['status'] ?? null)) {
                        $query->whereIn('status', $ids);
                    }

                    if ($ids = SelectedFilterIds::make($this->filters['department_name'] ?? null)) {
                        $query->whereIn('department_name', $ids);
                    }

                    if ($ids = SelectedFilterIds::make($this->filters['employee_id'] ?? null)) {
                        $query->whereIn('employee_id', $ids);
                    }

                    $this->applyEvaluationScope($query);

                    $filename = 'Monitoring-Evaluasi-Karyawan-'.now()->format('Y-m-d_His').'.xlsx';

                    return Excel::download(new EvaluationsMonitoringExport($query->get()), $filename);
                }),
        ];
    }

    protected static function getPagePermission(): ?string
    {
        return 'page_evaluation_performance_evaluation_dashboard';
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user || ! method_exists($user, 'hasRole')) {
            return false;
        }

        // Aggregated evaluation dashboard is for Admin / HRD / Operation Manager only.
        if (AdminAccess::check($user)
            || $user->hasRole('HRD')
            || $user->hasRole('Operation Manager')) {
            return parent::canAccess() || $user->can(static::getPagePermission());
        }

        return false;
    }

    public static function getNavigationLabel(): string
    {
        return 'Dashboard Evaluasi Kinerja';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Dashboard Evaluasi Kinerja & Penilaian Karyawan';
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-presentation-chart-line';
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
        $allLabel = str_starts_with((string) app()->getLocale(), 'id') ? 'Semua' : 'All';

        return $schema
            ->components([
                Section::make()
                    ->columns([
                        'default' => 1,
                        'sm'      => 2,
                        'md'      => 4,
                    ])
                    ->schema([
                        Select::make('evaluation_type')
                            ->label('Jenis / Periode Evaluasi')
                            ->native()
                            ->options(EvaluationType::options())
                            ->placeholder($allLabel),
                        Select::make('status')
                            ->label('Status Evaluasi')
                            ->native()
                            ->options(EvaluationStatus::options())
                            ->placeholder($allLabel),
                        Select::make('department_name')
                            ->label('Departemen')
                            ->native()
                            ->options(fn (): array => Department::query()->pluck('name', 'name')->all())
                            ->placeholder($allLabel),
                        Select::make('employee_id')
                            ->label('Karyawan')
                            ->native()
                            ->options(function (): array {
                                $query = Employee::query()->orderBy('name');

                                $scope = static::evaluationDashboardScope();

                                if ($scope !== null) {
                                    $query->whereIn('id', $scope['employeeIds'] ?: [0]);
                                }

                                return $query->pluck('name', 'id')->all();
                            })
                            ->placeholder($allLabel),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    protected function normalizeNativeFilterValues(): void
    {
        if (! is_array($this->filters)) {
            return;
        }

        foreach (['evaluation_type', 'status', 'department_name', 'employee_id'] as $key) {
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

    public function getWidgets(): array
    {
        return [
            EvaluationStatsOverviewWidget::class,
            EvaluationByTypeChart::class,
            EvaluationRecommendationChart::class,
            EvaluationApprovalProgressWidget::class,
        ];
    }
}
