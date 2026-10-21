<?php

namespace Webkul\Security\Filament\Resources\TeamResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\Team;

class TeamInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->icon('heroicon-o-user-group')
                    ->placeholder('—')
                    ->label(__('security::filament/resources/team.infolist.entries.name')),
                TextEntry::make('employee_members')
                    ->icon('heroicon-o-users')
                    ->badge()
                    ->placeholder('—')
                    ->label(__('security::filament/resources/team.infolist.entries.members'))
                    ->state(function (Team $record): array {
                        if (! class_exists(Employee::class)) {
                            return $record->users->pluck('name')->all();
                        }

                        $ids = TeamForm::assignedEmployeeIds($record);

                        if ($ids === []) {
                            return $record->users->pluck('name')->all();
                        }

                        return Employee::query()
                            ->whereIn('id', $ids)
                            ->orderBy('name')
                            ->pluck('name')
                            ->all();
                    }),
            ]);
    }
}
