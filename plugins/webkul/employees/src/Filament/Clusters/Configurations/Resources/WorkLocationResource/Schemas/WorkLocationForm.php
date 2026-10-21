<?php

namespace Webkul\Employee\Filament\Clusters\Configurations\Resources\WorkLocationResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Webkul\Employee\Data\IndonesianCities;
use Webkul\Employee\Enums\WorkLocation as WorkLocationEnum;

class WorkLocationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.name'))
                    ->required()
                    ->maxLength(255),
                ToggleButtons::make('location_type')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.location-type'))
                    ->inline()
                    ->options(WorkLocationEnum::class)
                    ->required(),
                TextInput::make('location_number')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.location-number')),
                Select::make('city')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.city'))
                    ->helperText(__('employees::filament/clusters/configurations/resources/work-location.form.city-help'))
                    ->options(fn (Get $get): array => IndonesianCities::options($get('city')))
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set, mixed $state): void {
                        if (! filled($state)) {
                            return;
                        }

                        $coordinates = IndonesianCities::coordinates((string) $state);

                        if ($coordinates === null) {
                            return;
                        }

                        $set('latitude', $coordinates['latitude']);
                        $set('longitude', $coordinates['longitude']);
                    }),
                TextInput::make('latitude')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.latitude'))
                    ->helperText(__('employees::filament/clusters/configurations/resources/work-location.form.coordinates-help'))
                    ->numeric()
                    ->minValue(-90)
                    ->maxValue(90)
                    ->step(0.0000001),
                TextInput::make('longitude')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.longitude'))
                    ->numeric()
                    ->minValue(-180)
                    ->maxValue(180)
                    ->step(0.0000001),
                Select::make('company_id')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.company'))
                    ->required()
                    ->preload()
                    ->default(current_company_id())
                    ->relationship('company', 'name'),
                Toggle::make('is_active')
                    ->label(__('employees::filament/clusters/configurations/resources/work-location.form.status'))
                    ->default(true)
                    ->required(),
            ]);
    }
}
