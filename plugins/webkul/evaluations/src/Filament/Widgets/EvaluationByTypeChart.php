<?php

namespace Webkul\Evaluation\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Evaluation\Enums\EvaluationType;
use Webkul\Evaluation\Models\Evaluation;

class EvaluationByTypeChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected ?string $maxHeight = '270px';

    protected static ?int $sort = 2;

    public function getHeading(): string|Htmlable|null
    {
        return 'Distribusi Jenis & Periode Evaluasi';
    }

    protected function getData(): array
    {
        $labels = [];
        $data = [];

        foreach (EvaluationType::cases() as $type) {
            $query = Evaluation::query()->where('evaluation_type', $type->value);

            if (! empty($this->pageFilters['status'])) {
                $query->whereIn('status', (array) $this->pageFilters['status']);
            }

            if (! empty($this->pageFilters['department_name'])) {
                $query->whereIn('department_name', (array) $this->pageFilters['department_name']);
            }

            if (! empty($this->pageFilters['employee_id'])) {
                $query->whereIn('employee_id', (array) $this->pageFilters['employee_id']);
            }

            if (! empty($this->pageFilters['startDate'])) {
                $query->whereDate('period_start', '>=', $this->pageFilters['startDate']);
            }

            if (! empty($this->pageFilters['endDate'])) {
                $query->whereDate('period_end', '<=', $this->pageFilters['endDate']);
            }

            $labels[] = $type->getLabel();
            $data[] = $query->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Jumlah Evaluasi',
                    'data'            => $data,
                    'backgroundColor' => [
                        '#3b82f6',
                        '#6366f1',
                        '#8b5cf6',
                        '#10b981',
                        '#f59e0b',
                        '#ec4899',
                        '#64748b',
                    ],
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
