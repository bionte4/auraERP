<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Evaluation\Exports\EvaluationsMonitoringExport;
use Webkul\Evaluation\Filament\Pages\PerformanceEvaluationDashboard;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;
use Webkul\Evaluation\Filament\Widgets\EvaluationByTypeChart;
use Webkul\Evaluation\Filament\Widgets\EvaluationRecommendationChart;
use Webkul\Evaluation\Filament\Widgets\EvaluationStatsOverviewWidget;
use Webkul\Evaluation\Models\Evaluation;

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
