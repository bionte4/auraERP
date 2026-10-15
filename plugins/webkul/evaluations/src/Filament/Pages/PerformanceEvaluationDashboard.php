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
use Webkul\Evaluation\Filament\Widgets\EvaluationApprovalProgressWidget;
use Webkul\Evaluation\Filament\Widgets\EvaluationByTypeChart;
use Webkul\Evaluation\Filament\Widgets\EvaluationRecommendationChart;
use Webkul\Evaluation\Filament\Widgets\EvaluationStatsOverviewWidget;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Support\Enums\NavigationGroup;

class PerformanceEvaluationDashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;
    use HasPageShield;

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

                    if (! empty($this->filters['evaluation_type'])) {
                        $query->whereIn('evaluation_type', (array) $this->filters['evaluation_type']);
                    }

                    if (! empty($this->filters['status'])) {
                        $query->whereIn('status', (array) $this->filters['status']);
                    }

                    if (! empty($this->filters['department_name'])) {
                        $query->whereIn('department_name', (array) $this->filters['department_name']);
                    }

                    if (! empty($this->filters['employee_id'])) {
                        $query->whereIn('employee_id', (array) $this->filters['employee_id']);
                    }

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

        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasRole') && $user->hasRole(config('filament-shield.super_admin.name', 'Admin'))) {
            return true;
        }

        return $user->can('view_any_evaluation_evaluation')
            || $user->can('view_any_performance_performance::plan')
            || $user->can(static::getPagePermission());
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

    public function filtersForm(Schema $schema): Schema
    {
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
                            ->multiple()
                            ->options(EvaluationType::options())
                            ->live(),
                        Select::make('status')
                            ->label('Status Evaluasi')
                            ->multiple()
                            ->options(EvaluationStatus::options())
                            ->live(),
                        Select::make('department_name')
                            ->label('Departemen')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => Department::query()->pluck('name', 'name')->all())
                            ->live(),
                        Select::make('employee_id')
                            ->label('Karyawan')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn (): array => Employee::query()->pluck('name', 'id')->all())
                            ->live(),
                    ])
                    ->columnSpanFull(),
            ]);
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
