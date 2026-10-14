<?php

namespace Webkul\Attendance\Filament\Resources;

use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Pages\ManageAttendances;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Schemas\AttendanceForm;
use Webkul\Attendance\Filament\Resources\AttendanceResource\Tables\AttendancesTable;
use Webkul\Attendance\Models\Attendance;
use Webkul\Support\Enums\NavigationGroup;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'work_date';

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/resources/attendance.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }

    public static function getModelLabel(): string
    {
        return __('attendances::filament/resources/attendance.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('attendances::filament/resources/attendance.plural');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employee', 'checkInSite', 'checkOutSite'])->withCount('tracks');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee.name', 'work_date', 'note'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->employee?->name ?? __('attendances::filament/resources/attendance.model');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('attendances::filament/resources/attendance.table.columns.date')   => $record->work_date?->toDateString() ?? '—',
            __('attendances::filament/resources/attendance.table.columns.status') => $record->status?->getLabel() ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAttendances::route('/'),
        ];
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'attendances';
    }
}
