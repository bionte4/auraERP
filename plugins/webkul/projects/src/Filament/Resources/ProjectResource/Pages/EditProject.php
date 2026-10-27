<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Services\ProjectGovernance;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getSavedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('projects::filament/resources/project/pages/edit-project.notification.title'))
            ->body(__('projects::filament/resources/project/pages/edit-project.notification.body'));
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['is_strategic'] ?? false) {
            $data['team_leader_employee_id'] = null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        ProjectGovernance::assertStrategicUnits($this->getRecord());
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            DeleteAction::make()
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('projects::filament/resources/project/pages/edit-project.header-actions.delete.notification.title'))
                        ->body(__('projects::filament/resources/project/pages/edit-project.header-actions.delete.notification.body')),
                ),
        ];
    }
}
