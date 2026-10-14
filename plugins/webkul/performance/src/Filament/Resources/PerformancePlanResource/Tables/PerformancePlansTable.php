<?php

namespace Webkul\Performance\Filament\Resources\PerformancePlanResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Performance\Enums\PlanStatus;
use Webkul\Performance\Models\PerformancePlan;

class PerformancePlansTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('performance::filament/resources/performance-plan.table.columns.title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.name')
                    ->label(__('performance::filament/resources/performance-plan.table.columns.employee'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('period')
                    ->label(__('performance::filament/resources/performance-plan.table.columns.period'))
                    ->state(fn (PerformancePlan $record): string => $record->periodLabel()),
                TextColumn::make('status')
                    ->label(__('performance::filament/resources/performance-plan.table.columns.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('score')
                    ->label(__('performance::filament/resources/performance-plan.table.columns.score'))
                    ->state(function (PerformancePlan $record): string {
                        $score = $record->weightedScore();

                        return $score === null ? '—' : number_format($score, 1);
                    }),
            ])
            ->defaultSort('period_start', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label(__('performance::filament/resources/performance-plan.table.filters.status'))
                    ->options(PlanStatus::options()),
                SelectFilter::make('employee_id')
                    ->label(__('performance::filament/resources/performance-plan.table.filters.employee'))
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
            ->emptyStateHeading(__('performance::filament/resources/performance-plan.table.empty.heading'))
            ->emptyStateDescription(__('performance::filament/resources/performance-plan.table.empty.description'));
    }
}
