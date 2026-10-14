<?php

namespace Webkul\Attendance\Filament\Resources;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Attendance\Filament\Resources\AttendanceSiteResource\Pages\ManageAttendanceSites;
use Webkul\Attendance\Models\AttendanceSite;
use Webkul\Support\Enums\NavigationGroup;

class AttendanceSiteResource extends Resource
{
    protected static ?string $model = AttendanceSite::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationParentItem(): ?string
    {
        return __('attendances::filament/resources/attendance.navigation.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/resources/site.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }

    public static function getModelLabel(): string
    {
        return __('attendances::filament/resources/site.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('attendances::filament/resources/site.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('attendances::filament/resources/site.form.name'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('latitude')
                    ->label(__('attendances::filament/resources/site.form.latitude'))
                    ->numeric()
                    ->required()
                    ->minValue(-90)
                    ->maxValue(90)
                    ->step(0.0000001),
                TextInput::make('longitude')
                    ->label(__('attendances::filament/resources/site.form.longitude'))
                    ->numeric()
                    ->required()
                    ->minValue(-180)
                    ->maxValue(180)
                    ->step(0.0000001),
                TextInput::make('radius_meters')
                    ->label(__('attendances::filament/resources/site.form.radius'))
                    ->helperText(__('attendances::filament/resources/site.form.radius-help'))
                    ->numeric()
                    ->required()
                    ->integer()
                    ->minValue(20)
                    ->maxValue(5000)
                    ->default(200)
                    ->suffix('m'),
                Toggle::make('is_active')
                    ->label(__('attendances::filament/resources/site.form.active'))
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('attendances::filament/resources/site.table.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('radius_meters')
                    ->label(__('attendances::filament/resources/site.table.radius'))
                    ->suffix(' m')
                    ->sortable(),
                TextColumn::make('coordinates')
                    ->label(__('attendances::filament/resources/site.table.coordinates'))
                    ->state(fn (AttendanceSite $record): string => number_format((float) $record->latitude, 5).', '.number_format((float) $record->longitude, 5))
                    ->url(fn (AttendanceSite $record): string => $record->mapUrl(), shouldOpenInNewTab: true),
                IconColumn::make('is_active')
                    ->label(__('attendances::filament/resources/site.table.active'))
                    ->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->defaultSort('name');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAttendanceSites::route('/'),
        ];
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'attendance-sites';
    }
}
