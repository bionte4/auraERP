<?php

namespace Webkul\Performance\Filament\Resources\PerformancePlanResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Models\Employee;
use Webkul\Performance\Enums\PlanStatus;

class PerformancePlanForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('performance::filament/resources/performance-plan.form.section'))
                    ->schema([
                        TextInput::make('title')
                            ->label(__('performance::filament/resources/performance-plan.form.title'))
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Select::make('employee_id')
                            ->label(__('performance::filament/resources/performance-plan.form.employee'))
                            ->relationship('employee', 'name')
                            ->preload()
                            ->required()
                            ->default(fn (): ?int => Employee::query()->where('user_id', Auth::id())->value('id')),
                        Select::make('status')
                            ->label(__('performance::filament/resources/performance-plan.form.status'))
                            ->options(PlanStatus::options())
                            ->required()
                            ->default(PlanStatus::Draft->value),
                        DatePicker::make('period_start')
                            ->label(__('performance::filament/resources/performance-plan.form.period-start'))
                            ->required()
                            ->native(false)
                            ->default(now()->startOfQuarter()->toDateString()),
                        DatePicker::make('period_end')
                            ->label(__('performance::filament/resources/performance-plan.form.period-end'))
                            ->required()
                            ->native(false)
                            ->default(now()->endOfQuarter()->toDateString())
                            ->minDate(fn (Get $get): mixed => $get('period_start')),
                        Textarea::make('summary')
                            ->label(__('performance::filament/resources/performance-plan.form.summary'))
                            ->helperText(__('performance::filament/resources/performance-plan.form.summary-helper'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }
}
