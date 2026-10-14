<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Webkul\Attendance\Enums\AttendanceStatus;
use Webkul\Employee\Models\Employee;

class AttendanceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('attendances::filament/resources/attendance.form.section'))
                    ->schema([
                        Select::make('employee_id')
                            ->label(__('attendances::filament/resources/attendance.form.employee'))
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->default(fn (): ?int => Employee::query()->where('user_id', Auth::id())->value('id')),
                        DatePicker::make('work_date')
                            ->label(__('attendances::filament/resources/attendance.form.date'))
                            ->required()
                            ->native(false)
                            ->default(now()->toDateString()),
                        Select::make('status')
                            ->label(__('attendances::filament/resources/attendance.form.status'))
                            ->options(AttendanceStatus::options())
                            ->required()
                            ->default(AttendanceStatus::Present->value)
                            ->helperText(__('attendances::filament/resources/attendance.form.late-helper')),
                        DateTimePicker::make('check_in')
                            ->label(__('attendances::filament/resources/attendance.form.check-in'))
                            ->native(false)
                            ->seconds(false),
                        DateTimePicker::make('check_out')
                            ->label(__('attendances::filament/resources/attendance.form.check-out'))
                            ->native(false)
                            ->seconds(false)
                            ->rules([
                                fn (Get $get): \Closure => function (string $attribute, mixed $value, \Closure $fail) use ($get): void {
                                    if (! $value || ! $get('check_in')) {
                                        return;
                                    }

                                    if (Carbon::parse($value)->lt(Carbon::parse($get('check_in')))) {
                                        $fail(__('attendances::filament/resources/attendance.form.check-out-after'));
                                    }
                                },
                            ]),
                        Textarea::make('note')
                            ->label(__('attendances::filament/resources/attendance.form.note'))
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }
}
