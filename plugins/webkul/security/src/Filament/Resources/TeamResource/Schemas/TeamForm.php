<?php

namespace Webkul\Security\Filament\Resources\TeamResource\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Schema as DbSchema;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Team;
use Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList;

class TeamForm
{
    /**
     * Names of employees on the team's project(s) who cannot mirror
     * into the team because they have no Related User yet.
     *
     * @return array<int, string>
     */
    protected static function unsyncedEmployeeNames(?Team $record): array
    {
        $synchronizerClass = 'Webkul\\Project\\Services\\TeamMembershipSynchronizer';

        if (! $record || ! class_exists($synchronizerClass)) {
            return [];
        }

        try {
            return app($synchronizerClass)->unsyncedEmployeeNamesForTeam($record);
        } catch (\Throwable) {
            return [];
        }
    }

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
                Placeholder::make('unsynced_employees')
                    ->label(__('security::filament/resources/team.form.fields.unsynced'))
                    ->helperText(__('security::filament/resources/team.form.fields.unsynced-help'))
                    ->content(fn (?Team $record): string => implode(', ', static::unsyncedEmployeeNames($record)))
                    ->visible(fn (?Team $record): bool => static::unsyncedEmployeeNames($record) !== [])
                    ->columnSpanFull(),
            ]);
    }
}
