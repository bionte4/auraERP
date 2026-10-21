<?php

namespace Webkul\Evaluation\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Evaluation\Filament\Widgets\Concerns\ScopesEvaluationDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Evaluation\Enums\RecommendationDecision;
use Webkul\Evaluation\Models\Evaluation;

class EvaluationRecommendationChart extends ChartWidget
{
    use InteractsWithPageFilters;
    use ScopesEvaluationDashboard;

    protected ?string $maxHeight = '270px';

    protected static ?int $sort = 3;

    public function getHeading(): string|Htmlable|null
    {
        return 'Komposisi Rekomendasi Keputusan (Poin I)';
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        foreach (RecommendationDecision::cases() as $decision) {
            $query = Evaluation::query()->where('recommendation_decision', $decision->value);

            if (! empty($this->pageFilters['evaluation_type'])) {
                $query->whereIn('evaluation_type', (array) $this->pageFilters['evaluation_type']);
            }

            if (! empty($this->pageFilters['status'])) {
                $query->whereIn('status', (array) $this->pageFilters['status']);
            }

            if (! empty($this->pageFilters['department_name'])) {
                $query->whereIn('department_name', (array) $this->pageFilters['department_name']);
            }

            if (! empty($this->pageFilters['employee_id'])) {
                $query->whereIn('employee_id', (array) $this->pageFilters['employee_id']);
            }

            $this->applyEvaluationScope($query);

            $labels[] = $decision->getLabel();
            $data[] = $query->count();
        }

        $undecidedQuery = Evaluation::query()->whereNull('recommendation_decision');

        if (! empty($this->pageFilters['evaluation_type'])) {
            $undecidedQuery->whereIn('evaluation_type', (array) $this->pageFilters['evaluation_type']);
        }

        $this->applyEvaluationScope($undecidedQuery);

        $labels[] = 'Belum Ditentukan';
        $data[] = $undecidedQuery->count();

        return [
            'datasets' => [
                [
                    'label'           => 'Jumlah Karyawan',
                    'data'            => $data,
                    'backgroundColor' => [
                        '#22c55e', // Diangkat Tetap
                        '#3b82f6', // Diperpanjang PKWT
                        '#f59e0b', // Dialihkan ke Outtask/Outsource
                        '#06b6d4', // Diperpanjang Outtask/Outsource
                        '#f97316', // Tidak Diperpanjang Outtask/Outsource
                        '#ef4444', // Terminasi PKWT
                        '#94a3b8', // Belum Ditentukan
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
