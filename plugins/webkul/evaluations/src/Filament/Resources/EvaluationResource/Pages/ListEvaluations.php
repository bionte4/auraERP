<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Evaluation\Filament\Pages\PerformanceEvaluationDashboard;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;
use Webkul\Evaluation\Filament\Widgets\EvaluationByTypeChart;
use Webkul\Evaluation\Filament\Widgets\EvaluationRecommendationChart;
use Webkul\Evaluation\Filament\Widgets\EvaluationStatsOverviewWidget;

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
