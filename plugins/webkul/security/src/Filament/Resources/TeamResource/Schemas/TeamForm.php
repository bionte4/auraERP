<?php

namespace Webkul\Security\Filament\Resources\TeamResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Schema as DbSchema;
use Webkul\Project\Models\Project;
use Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList;

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
                Select::make('project_id')
                    ->label(__('security::filament/resources/team.form.fields.project'))
                    ->helperText(__('security::filament/resources/team.form.fields.project-help'))
                    ->relationship('project', 'name')
                    ->preload()
                    ->nullable()
                    ->visible(fn (): bool => class_exists(Project::class)
                        && DbSchema::hasTable('projects_projects')
                        && DbSchema::hasColumn('teams', 'project_id'))
                    ->columnSpanFull(),
                // Livewire search — CheckboxList::searchable() needs Alpine and often fails to filter.
                ...LivewireSearchableCheckboxList::make(
                    name: 'users',
                    relationship: 'users',
                    titleAttribute: 'name',
                    label: __('security::filament/resources/team.form.fields.members'),
                    helperText: __('security::filament/resources/team.form.fields.members-help'),
                    searchPlaceholder: __('Search members...'),
                ),
            ]);
    }
}
