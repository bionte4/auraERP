<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Evaluation\Enums\EvaluationStatus;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('evaluations::filament/resources/evaluation.form.section'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('evaluations::filament/resources/evaluation.form.title'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('employee_id')
                            ->label(__('evaluations::filament/resources/evaluation.form.employee'))
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('reviewer_id')
                            ->label(__('evaluations::filament/resources/evaluation.form.reviewer'))
                            ->relationship('reviewer', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('status')
                            ->label(__('evaluations::filament/resources/evaluation.form.status'))
                            ->options(EvaluationStatus::options())
                            ->required()
                            ->default(EvaluationStatus::Draft->value),
                        DatePicker::make('period_start')
                            ->label(__('evaluations::filament/resources/evaluation.form.period-start'))
                            ->required()
                            ->native(false)
                            ->default(now()->startOfQuarter()->toDateString()),
                        DatePicker::make('period_end')
                            ->label(__('evaluations::filament/resources/evaluation.form.period-end'))
                            ->required()
                            ->native(false)
                            ->default(now()->endOfQuarter()->toDateString())
                            ->minDate(fn (Get $get): mixed => $get('period_start')),
                        Textarea::make('summary')
                            ->label(__('evaluations::filament/resources/evaluation.form.summary'))
                            ->helperText(__('evaluations::filament/resources/evaluation.form.summary-helper'))
                            ->columnSpanFull(),
                        Textarea::make('employee_comment')
                            ->label(__('evaluations::filament/resources/evaluation.form.employee-comment'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }
}
