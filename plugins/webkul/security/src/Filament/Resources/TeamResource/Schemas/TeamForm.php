<?php

namespace Webkul\Security\Filament\Resources\TeamResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class TeamForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('security::filament/resources/team.form.fields.name'))
                    ->required()
                    ->maxLength(255),
                Select::make('users')
                    ->label(__('security::filament/resources/team.form.fields.members'))
                    ->helperText(__('security::filament/resources/team.form.fields.members-help'))
                    ->relationship('users', 'name')
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->columnSpanFull(),
            ]);
    }
}
