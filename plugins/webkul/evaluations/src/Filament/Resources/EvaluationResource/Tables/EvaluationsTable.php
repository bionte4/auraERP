<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Evaluation\Enums\EvaluationStatus;
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
                    ->state(function (Evaluation $record): string {
                        $score = $record->finalScore();

                        return $score === null ? '—' : number_format($score, 1);
                    }),
            ])
            ->defaultSort('period_start', 'desc')
            ->filters([
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
