<?php

namespace Webkul\Performance\Filament\Resources\PerformancePlanResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Performance\Models\PerformancePlan;

class PerformancePlanInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('performance::filament/resources/performance-plan.infolist.section'))
                    ->schema([
                        TextEntry::make('title')
                            ->label(__('performance::filament/resources/performance-plan.form.title')),
                        TextEntry::make('employee.name')
                            ->label(__('performance::filament/resources/performance-plan.form.employee'))
                            ->placeholder('—'),
                        TextEntry::make('status')
                            ->label(__('performance::filament/resources/performance-plan.form.status'))
                            ->badge(),
                        TextEntry::make('period')
                            ->label(__('performance::filament/resources/performance-plan.table.columns.period'))
                            ->state(fn (PerformancePlan $record): string => $record->periodLabel()),
                        TextEntry::make('score')
                            ->label(__('performance::filament/resources/performance-plan.table.columns.score'))
                            ->state(function (PerformancePlan $record): string {
                                $score = $record->weightedScore();

                                return $score === null ? '—' : number_format($score, 1);
                            })
                            ->helperText(__('performance::filament/resources/performance-plan.form.score-helper')),
                        TextEntry::make('summary')
                            ->label(__('performance::filament/resources/performance-plan.form.summary'))
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
