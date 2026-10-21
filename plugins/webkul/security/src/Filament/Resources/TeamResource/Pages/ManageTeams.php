<?php

namespace Webkul\Security\Filament\Resources\TeamResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Filament\Resources\TeamResource;
use Webkul\Security\Filament\Resources\TeamResource\Schemas\TeamForm;
use Webkul\Security\Models\Team;

class ManageTeams extends ManageRecords
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->icon('heroicon-o-plus-circle')
                ->using(function (array $data): Model {
                    return $this->persistTeam($data);
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('security::filament/resources/team/pages/manage-team.header-actions.create.notification.title'))
                        ->body(__('security::filament/resources/team/pages/manage-team.header-actions.create.notification.body'))
                ),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public function persistTeam(array $data, ?Team $team = null): Team
    {
        $employeeIds = $data['employees'] ?? [];
        unset($data['employees'], $data['employees_search']);

        $team ??= new Team;
        $team->fill($data);
        $team->save();

        TeamForm::syncEmployees($team, is_array($employeeIds) ? $employeeIds : []);

        return $team;
    }
}
