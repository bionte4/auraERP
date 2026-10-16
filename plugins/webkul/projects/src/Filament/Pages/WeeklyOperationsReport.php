<?php

namespace Webkul\Project\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Employee\Models\Employee;
use Webkul\Project\Exports\WeeklyOperationsExport;
use Webkul\Project\Services\EmployeeCapacity;
use Webkul\Support\Enums\NavigationGroup;

class WeeklyOperationsReport extends Page
{
    use HasPageShield;

    protected string $view = 'projects::filament.pages.weekly-operations-report';

    protected static ?string $slug = 'weekly-operations-report';

    protected static ?int $navigationSort = 3;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static function getPagePermission(): ?string
    {
        return 'page_project_weekly_operations_report';
    }

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/pages/weekly-operations-report.navigation.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('projects::filament/pages/weekly-operations-report.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')
                ->label(__('projects::filament/pages/weekly-operations-report.actions.download'))
                ->icon('heroicon-o-arrow-down-tray')
                ->action(fn () => $this->downloadReport()),
        ];
    }

    public function downloadReport(): BinaryFileResponse
    {
        $filename = 'weekly-operations-'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(new WeeklyOperationsExport, $filename);
    }

    /**
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        $export = new WeeklyOperationsExport;
        $rows = $export->collection();

        return [
            'rows'     => $rows->map(fn ($row): array => [
                'metric' => $row[0],
                'value'  => $row[1],
                'notes'  => $row[2],
            ])->all(),
            'loadRows' => $this->capacityLoadRows(),
        ];
    }

    /**
     * @return array<int, array{name: string, projects: int, hours: float}>
     */
    protected function capacityLoadRows(): array
    {
        if (! class_exists(Employee::class) || ! Schema::hasTable('projects_project_employees')) {
            return [];
        }

        $employees = Employee::query()
            ->where('is_active', true)
            ->whereHas('projects')
            ->orderBy('name')
            ->limit(40)
            ->get(['id', 'name']);

        $capacity = app(EmployeeCapacity::class)->forEmployees($employees);

        return $employees->map(fn (Employee $employee): array => [
            'name'     => $employee->name,
            'projects' => $capacity[$employee->id]['projects'] ?? 0,
            'hours'    => $capacity[$employee->id]['hours'] ?? 0.0,
        ])->all();
    }
}
