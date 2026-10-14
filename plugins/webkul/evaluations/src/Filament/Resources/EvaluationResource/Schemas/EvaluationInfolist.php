<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Evaluation\Models\Evaluation;

class EvaluationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('evaluations::filament/resources/evaluation.infolist.section'))
                    ->schema([
                        TextEntry::make('title')
                            ->label(__('evaluations::filament/resources/evaluation.form.title')),
                        TextEntry::make('employee.name')
                            ->label(__('evaluations::filament/resources/evaluation.form.employee'))
                            ->placeholder('—'),
                        TextEntry::make('reviewer.name')
                            ->label(__('evaluations::filament/resources/evaluation.form.reviewer'))
                            ->placeholder('—'),
                        TextEntry::make('status')
                            ->label(__('evaluations::filament/resources/evaluation.form.status'))
                            ->badge(),
                        TextEntry::make('period')
                            ->label(__('evaluations::filament/resources/evaluation.table.columns.period'))
                            ->state(fn (Evaluation $record): string => $record->periodLabel()),
                        TextEntry::make('score')
                            ->label(__('evaluations::filament/resources/evaluation.table.columns.score'))
                            ->state(function (Evaluation $record): string {
                                $score = $record->finalScore();

                                return $score === null ? '—' : number_format($score, 1);
                            })
                            ->helperText(__('evaluations::filament/resources/evaluation.form.score-helper')),
                        TextEntry::make('summary')
                            ->label(__('evaluations::filament/resources/evaluation.form.summary'))
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('employee_comment')
                            ->label(__('evaluations::filament/resources/evaluation.form.employee-comment'))
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
