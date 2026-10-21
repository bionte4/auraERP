<?php

namespace Webkul\Evaluation\Filament\Widgets;

use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Webkul\Evaluation\Filament\Widgets\Concerns\ScopesEvaluationDashboard;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Evaluation\Exports\EvaluationsMonitoringExport;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;
use Webkul\Evaluation\Models\Evaluation;

class EvaluationApprovalProgressWidget extends BaseWidget
{
    use InteractsWithPageFilters;
    use ScopesEvaluationDashboard;

    protected static bool $isLazy = false;

    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected function getTableHeading(): string|Htmlable|null
    {
        return 'Monitoring Evaluasi & Tahapan Approval Berjenjang (L1 -> Karyawan -> L2)';
    }

    /**
     * @return Builder<Evaluation>
     */
    protected function buildQuery(): Builder
    {
        $query = Evaluation::query()->with(['employee.department', 'employee.employmentType', 'reviewer', 'gmApprover', 'ratings']);

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

        return $this->applyEvaluationScope($query)->latest('updated_at');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->buildQuery())
            ->defaultPaginationPageOption(10)
            ->headerActions([
                Action::make('export_excel')
                    ->label('Download Excel (.xlsx)')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function (): BinaryFileResponse {
                        $records = $this->buildQuery()->get();
                        $filename = 'Monitoring-Evaluasi-Karyawan-'.now()->format('Y-m-d_His').'.xlsx';

                        return Excel::download(new EvaluationsMonitoringExport($records), $filename);
                    }),
            ])
            ->columns([
                TextColumn::make('employee.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('department_name')
                    ->label('Departemen')
                    ->placeholder('—'),
                TextColumn::make('evaluation_type')
                    ->label('Jenis Evaluasi')
                    ->badge(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge(),
                TextColumn::make('goal_score')
                    ->label('Pencapaian Goal')
                    ->badge()
                    ->color(fn (Evaluation $record): string => $record->isOutsourceOrOuttask() ? 'gray' : ($record->meetsGoalCriterion() ? 'success' : 'warning'))
                    ->state(function (Evaluation $record): string {
                        if ($record->isOutsourceOrOuttask()) {
                            return 'N/A (Outsource)';
                        }

                        $goal = $record->resolvedGoalAchievementPercentage();

                        return $goal !== null ? number_format($goal, 1).'%' : '—';
                    }),
                TextColumn::make('work_score')
                    ->label('Total / Rata-rata Poin D')
                    ->badge()
                    ->color(fn (Evaluation $record): string => $record->meetsWorkManagementCriterion() ? 'success' : 'warning')
                    ->state(function (Evaluation $record): string {
                        $score = $record->finalScore();

                        return $score !== null
                            ? $record->totalRatingScore().' / '.number_format($score, 2)
                            : '0 / —';
                    }),
                TextColumn::make('competency_score')
                    ->label('Kompetensi')
                    ->badge()
                    ->color(fn (Evaluation $record): string => $record->meetsCompetencyCriterion() ? 'success' : 'warning')
                    ->state(function (Evaluation $record): string {
                        $comp = $record->competencyAchievementPercentage();

                        return $comp !== null ? number_format($comp, 1).'%' : '—';
                    }),
                TextColumn::make('recommendation_decision')
                    ->label('Keputusan (Poin I)')
                    ->badge()
                    ->placeholder('Belum ditentukan'),
                TextColumn::make('approval_stage')
                    ->label('Tahapan Approval')
                    ->badge()
                    ->color(fn (Evaluation $record): string => str_contains($record->approvalStageLabel(), 'Selesai') ? 'success' : 'info')
                    ->state(fn (Evaluation $record): string => $record->approvalStageLabel()),
            ])
            ->recordActions([
                Action::make('open')
                    ->label('Buka')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Evaluation $record): string => EvaluationResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
