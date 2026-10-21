<?php

namespace Webkul\Evaluation\Filament\Widgets;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Performance\Models\PerformancePlan;
use Webkul\Evaluation\Filament\Widgets\Concerns\ScopesEvaluationDashboard;
use Webkul\PluginManager\Package;

class EvaluationStatsOverviewWidget extends BaseWidget
{
    use InteractsWithPageFilters;
    use ScopesEvaluationDashboard;

    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 1;

    protected function getHeading(): ?string
    {
        return 'Ringkasan Evaluasi Kinerja & Goal Setting';
    }

    /**
     * @return Builder<Evaluation>
     */
    protected function filteredQuery(): Builder
    {
        $query = Evaluation::query()->with(['ratings', 'employee.employmentType']);

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

        if (! empty($this->pageFilters['startDate'])) {
            $query->whereDate('period_start', '>=', $this->pageFilters['startDate']);
        }

        if (! empty($this->pageFilters['endDate'])) {
            $query->whereDate('period_end', '<=', $this->pageFilters['endDate']);
        }

        return $this->applyEvaluationScope($query);
    }

    protected function getStats(): array
    {
        $evaluations = $this->filteredQuery()->get();

        $total = $evaluations->count();
        $inProgress = $evaluations->where('status', EvaluationStatus::InReview)->count();
        $completed = $evaluations->where('status', EvaluationStatus::Completed)->count();

        $scoredEvaluations = $evaluations
            ->map(fn (Evaluation $eval): ?float => $eval->finalScore())
            ->filter(fn (?float $score): bool => $score !== null);

        $avgWorkScore = $scoredEvaluations->isNotEmpty()
            ? round($scoredEvaluations->avg(), 2)
            : 0.0;

        $pkwtEvaluations = $evaluations->reject(fn (Evaluation $eval): bool => $eval->isOutsourceOrOuttask());
        $eligibleCount = $pkwtEvaluations->filter(fn (Evaluation $eval): bool => $eval->isEligibleForPermanentAppointment())->count();
        $outsourceCount = $evaluations->filter(fn (Evaluation $eval): bool => $eval->isOutsourceOrOuttask())->count();

        $goalScores = $pkwtEvaluations
            ->map(fn (Evaluation $eval): ?float => $eval->resolvedGoalAchievementPercentage())
            ->filter(fn (?float $goal): bool => $goal !== null);

        $avgGoalPercentage = $goalScores->isNotEmpty()
            ? round($goalScores->avg(), 1)
            : null;

        $plansCount = 0;

        if (Package::isPluginInstalled('performance') && class_exists(PerformancePlan::class)) {
            $planQuery = PerformancePlan::query();
            // performance_plans has employee_id but no reviewer/approver
            // columns, so scope by employee only (no approver chain).
            $this->applyEvaluationScope($planQuery, false);
            $plansCount = $planQuery->count();
        }

        return [
            Stat::make('Total Evaluasi Kinerja', (string) $total)
                ->description("PKWT: {$pkwtEvaluations->count()} | Outtask/Outsource: {$outsourceCount}")
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('primary'),

            Stat::make('Status Evaluasi Berjalan', "{$inProgress} In Progress")
                ->description("{$completed} Selesai (Completed)")
                ->descriptionIcon('heroicon-m-clock')
                ->color($inProgress > 0 ? 'warning' : 'success'),

            Stat::make('Rata-rata Poin D (Manajemen Kerja)', number_format($avgWorkScore, 2).' / 5.00')
                ->description($avgWorkScore >= 3.0 ? 'Memenuhi Standar (≥ 3.00)' : 'Standar Kelulusan: ≥ 3.00')
                ->descriptionIcon('heroicon-m-star')
                ->color($avgWorkScore >= 3.0 ? 'success' : 'warning'),

            Stat::make('Kelayakan Pengangkatan Tetap', "{$eligibleCount} / {$pkwtEvaluations->count()} Karyawan")
                ->description(
                    $avgGoalPercentage !== null
                        ? "Rata-rata Goal: {$avgGoalPercentage}% ({$plansCount} Program Goal)"
                        : "Min. 2 dari 3 Kriteria Terpenuhi ({$plansCount} Program Goal)"
                )
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($eligibleCount > 0 ? 'success' : 'info'),
        ];
    }
}
