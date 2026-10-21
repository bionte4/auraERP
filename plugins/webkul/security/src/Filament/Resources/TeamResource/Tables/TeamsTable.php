<?php

namespace Webkul\Security\Filament\Resources\TeamResource\Tables;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Security\Filament\Resources\TeamResource\Pages\ManageTeams;
use Webkul\Security\Filament\Resources\TeamResource\Schemas\TeamForm;
use Webkul\Security\Models\Team;

class TeamsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('security::filament/resources/team.table.columns.name'))
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
                TextColumn::make('users_count')
                    ->counts('users')
                    ->label(__('security::filament/resources/team.table.columns.members'))
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('users.name')
                    ->label(__('security::filament/resources/team.table.columns.member-names'))
                    ->badge()
                    ->limitList(3)
                    ->toggleable(),
                TextColumn::make('creator.name')
                    ->label(__('security::filament/resources/team.table.columns.created-by'))
                    ->searchable()
                    ->limit(50)
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
                    ->using(function (Team $record, array $data): Team {
                        $page = app(ManageTeams::class);

                        return $page->persistTeam($data, $record);
                    })
                    ->fillForm(fn (Team $record): array => [
                        'name'       => $record->name,
                        'project_id' => $record->project_id,
                        'employees'  => TeamForm::assignedEmployeeIds($record),
                    ])
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/team.table.actions.edit.notification.title'))
                            ->body(__('security::filament/resources/team.table.actions.edit.notification.body'))
                    ),
                // Always visible to users allowed to delete (TeamPolicy).
                // Deleting a team that still has members used to hide this
                // action entirely, which looked like a bug even for Admin.
                // Instead, confirm explicitly and detach members first.
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading(__('security::filament/resources/team.table.actions.delete.modal.heading'))
                    ->modalDescription(fn (Team $record): string => $record->users->isNotEmpty()
                        ? __('security::filament/resources/team.table.actions.delete.modal.description-members', [
                            'name'  => $record->name,
                            'count' => $record->users->count(),
                        ])
                        : __('security::filament/resources/team.table.actions.delete.modal.description', [
                            'name' => $record->name,
                        ]))
                    ->modalSubmitActionLabel(__('security::filament/resources/team.table.actions.delete.modal.submit'))
                    ->action(function (Team $record): void {
                        $record->users()->detach();
                        $record->delete();

                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/team.table.actions.delete.notification.title'))
                            ->body(__('security::filament/resources/team.table.actions.delete.notification.body'))
                            ->send();
                    }),
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle')
                    ->using(function (array $data): Team {
                        return app(ManageTeams::class)->persistTeam($data);
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('security::filament/resources/team.table.empty-state-actions.create.notification.title'))
                            ->body(__('security::filament/resources/team.table.empty-state-actions.create.notification.body'))
                    ),
            ]);
    }
}
