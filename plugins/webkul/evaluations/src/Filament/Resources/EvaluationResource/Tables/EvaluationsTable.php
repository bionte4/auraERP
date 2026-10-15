<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Tables;

use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Enums\EvaluationType;
use Webkul\Evaluation\Models\Evaluation;

class EvaluationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('evaluations::filament/resources/evaluation.table.columns.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('evaluation_type')
                    ->label('Tipe')
                    ->badge()
                    ->toggleable(),
                TextColumn::make('employee.name')
                    ->label(__('evaluations::filament/resources/evaluation.table.columns.employee'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reviewer.name')
                    ->label(__('evaluations::filament/resources/evaluation.table.columns.reviewer'))
                    ->formatStateUsing(fn (?string $state): string => filled($state) ? $state : '—')
                    ->toggleable(),
                TextColumn::make('period')
                    ->label(__('evaluations::filament/resources/evaluation.table.columns.period'))
                    ->state(fn (Evaluation $record): string => $record->periodLabel()),
                TextColumn::make('status')
                    ->label(__('evaluations::filament/resources/evaluation.table.columns.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('score')
                    ->label(__('evaluations::filament/resources/evaluation.table.columns.score'))
                    ->badge()
                    ->color(fn (Evaluation $record): string => $record->meetsWorkManagementCriterion() ? 'success' : 'gray')
                    ->state(function (Evaluation $record): string {
                        $score = $record->finalScore();

                        return $score === null ? '—' : number_format($score, 1);
                    }),
                TextColumn::make('eligibility')
                    ->label('Kriteria PKWT')
                    ->badge()
                    ->color(fn (Evaluation $record): string => $record->isEligibleForPermanentAppointment() ? 'success' : 'warning')
                    ->state(fn (Evaluation $record): string => $record->criteriaMetCount().'/3 Kriteria')
                    ->toggleable(),
            ])
            ->defaultSort('period_start', 'desc')
            ->filters([
                SelectFilter::make('evaluation_type')
                    ->label('Jenis Evaluasi')
                    ->options(EvaluationType::options()),
                SelectFilter::make('status')
                    ->label(__('evaluations::filament/resources/evaluation.table.filters.status'))
                    ->options(EvaluationStatus::options()),
                SelectFilter::make('employee_id')
                    ->label(__('evaluations::filament/resources/evaluation.table.filters.employee'))
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('print_pdf')
                    ->label('Cetak PDF')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->action(function (Evaluation $record): StreamedResponse {
                        $record->loadMissing(['employee.department', 'employee.workLocation', 'reviewer', 'gmApprover', 'directorApprover', 'performancePlan', 'ratings']);
                        $pdf = Pdf::loadView('evaluations::pdf.pkwt-evaluation', ['record' => $record])
                            ->setPaper('a4', 'portrait');

                        $filename = 'Evaluasi-PKWT-'.str($record->employee?->name ?? 'Karyawan')->slug().'-'.$record->id.'.pdf';

                        return response()->streamDownload(
                            fn () => print ($pdf->output()),
                            $filename
                        );
                    }),
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('evaluations::filament/resources/evaluation.table.empty.heading'))
            ->emptyStateDescription(__('evaluations::filament/resources/evaluation.table.empty.description'));
    }
}
