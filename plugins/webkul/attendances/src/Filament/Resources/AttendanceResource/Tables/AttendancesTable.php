<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Attendance\Enums\AttendanceStatus;
use Webkul\Attendance\Models\Attendance;

class AttendancesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('work_date')
                    ->label(__('attendances::filament/resources/attendance.table.columns.date'))
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('employee.name')
                    ->label(__('attendances::filament/resources/attendance.table.columns.employee'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('check_in')
                    ->label(__('attendances::filament/resources/attendance.table.columns.check-in'))
                    ->dateTime('H:i'),
                TextColumn::make('check_out')
                    ->label(__('attendances::filament/resources/attendance.table.columns.check-out'))
                    ->dateTime('H:i'),
                TextColumn::make('checkInSite.name')
                    ->label(__('attendances::filament/resources/attendance.table.columns.site'))
                    ->toggleable(),
                TextColumn::make('check_in_location')
                    ->label(__('attendances::filament/resources/attendance.table.columns.check-in-location'))
                    ->state(fn (Attendance $record): string => $record->checkInMapUrl() ? number_format((float) $record->check_in_latitude, 5).', '.number_format((float) $record->check_in_longitude, 5) : '—')
                    ->url(fn (Attendance $record): ?string => $record->checkInMapUrl(), shouldOpenInNewTab: true)
                    ->toggleable(),
                TextColumn::make('check_out_location')
                    ->label(__('attendances::filament/resources/attendance.table.columns.check-out-location'))
                    ->state(fn (Attendance $record): string => $record->checkOutMapUrl() ? number_format((float) $record->check_out_latitude, 5).', '.number_format((float) $record->check_out_longitude, 5) : '—')
                    ->url(fn (Attendance $record): ?string => $record->checkOutMapUrl(), shouldOpenInNewTab: true)
                    ->toggleable(),
                TextColumn::make('tracks_count')
                    ->label(__('attendances::filament/resources/attendance.table.columns.tracks'))
                    ->numeric()
                    ->toggleable(),
                TextColumn::make('worked')
                    ->label(__('attendances::filament/resources/attendance.table.columns.worked'))
                    ->state(fn (Attendance $record): string => $record->workedLabel() ?? '—'),
                TextColumn::make('status')
                    ->label(__('attendances::filament/resources/attendance.table.columns.status'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('note')
                    ->label(__('attendances::filament/resources/attendance.table.columns.note'))
                    ->limit(40)
                    ->toggleable(),
            ])
            ->defaultSort('work_date', 'desc')
            ->groups([
                Group::make('work_date')
                    ->label(__('attendances::filament/resources/attendance.table.groups.date'))
                    ->date(),
                Group::make('employee.name')
                    ->label(__('attendances::filament/resources/attendance.table.groups.employee')),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('attendances::filament/resources/attendance.table.filters.status'))
                    ->options(AttendanceStatus::options()),
                SelectFilter::make('employee_id')
                    ->label(__('attendances::filament/resources/attendance.table.filters.employee'))
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('work_date')
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('attendances::filament/resources/attendance.table.filters.from'))
                            ->native(false),
                        DatePicker::make('until')
                            ->label(__('attendances::filament/resources/attendance.table.filters.until'))
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('work_date', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $query, string $date): Builder => $query->whereDate('work_date', '<=', $date));
                    }),
            ])
            ->recordActions([
                Action::make('tracks')
                    ->label(__('attendances::filament/resources/attendance.tracks.action'))
                    ->icon('heroicon-o-map-pin')
                    ->modalHeading(__('attendances::filament/resources/attendance.tracks.heading'))
                    ->modalFooterActions([])
                    ->modalContent(fn (Attendance $record) => view('attendances::filament.tracks', [
                        'tracks' => $record->tracks()->orderBy('recorded_at')->get(),
                    ]))
                    ->visible(fn (Attendance $record): bool => ($record->tracks_count ?? 0) > 0),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('attendances::filament/resources/attendance.table.empty.heading'))
            ->emptyStateDescription(__('attendances::filament/resources/attendance.table.empty.description'));
    }
}
