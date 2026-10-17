<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Evaluation\Exports\EvaluationsMonitoringExport;
use Webkul\Evaluation\Filament\Pages\PerformanceEvaluationDashboard;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;
use Webkul\Evaluation\Filament\Widgets\EvaluationByTypeChart;
use Webkul\Evaluation\Filament\Widgets\EvaluationRecommendationChart;
use Webkul\Evaluation\Filament\Widgets\EvaluationStatsOverviewWidget;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Evaluation\Services\EvaluationBulkImportService;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            EvaluationStatsOverviewWidget::class,
            EvaluationByTypeChart::class,
            EvaluationRecommendationChart::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label(__('evaluations::filament/resources/evaluation/pages/list.header-actions.template.label'))
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(fn () => app(EvaluationBulkImportService::class)->downloadTemplate()),
            Action::make('bulkImport')
                ->label(__('evaluations::filament/resources/evaluation/pages/list.header-actions.import.label'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading(__('evaluations::filament/resources/evaluation/pages/list.header-actions.import.modal-heading'))
                ->modalDescription(__('evaluations::filament/resources/evaluation/pages/list.header-actions.import.modal-description'))
                ->schema([
                    FileUpload::make('file')
                        ->label(__('evaluations::filament/resources/evaluation/pages/list.header-actions.import.file-label'))
                        ->disk('local')
                        ->directory('imports/evaluations')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $relativePath = (string) ($data['file'] ?? '');
                    $fullPath = Storage::disk('local')->path($relativePath);

                    $result = app(EvaluationBulkImportService::class)->importFromPath($fullPath);

                    Storage::disk('local')->delete($relativePath);

                    Notification::make()
                        ->success()
                        ->title(__('evaluations::filament/resources/evaluation/pages/list.header-actions.import.notification.title'))
                        ->body(__('evaluations::filament/resources/evaluation/pages/list.header-actions.import.notification.body', $result))
                        ->send();
                }),
            Action::make('download_excel')
                ->label('Download Excel (.xlsx)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action(function (): BinaryFileResponse {
                    $records = Evaluation::query()
                        ->with(['employee.department', 'employee.employmentType', 'reviewer', 'gmApprover', 'ratings'])
                        ->latest('updated_at')
                        ->get();

                    $filename = 'Monitoring-Evaluasi-Karyawan-'.now()->format('Y-m-d_His').'.xlsx';

                    return Excel::download(new EvaluationsMonitoringExport($records), $filename);
                }),
            Action::make('open_dashboard')
                ->label('Buka Dashboard Evaluasi')
                ->icon('heroicon-o-presentation-chart-line')
                ->color('info')
                ->url(fn (): string => PerformanceEvaluationDashboard::getUrl()),
            CreateAction::make()
                ->label(__('evaluations::filament/resources/evaluation/pages/list.header-actions.create'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
