<?php

namespace Webkul\Project\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Project\Exports\WeeklyOperationsExport;
use Webkul\Project\Services\WeeklyOperationsReportData;
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

    public static function canAccess(): bool
    {
        if (! parent::canAccess()) {
            return false;
        }

        return app(WeeklyOperationsReportData::class)->viewerMayAccess();
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

        return Excel::download(new WeeklyOperationsExport(auth()->user()), $filename);
    }

    /**
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        $report = app(WeeklyOperationsReportData::class)->build(auth()->user());

        return [
            'scopeLabel' => $report['scope_label'],
            'weekLabel'  => $report['week_label'],
            'rows'       => $report['metrics'],
            'loadRows'   => $report['load_rows'],
        ];
    }
}
