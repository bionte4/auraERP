<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Services\ProjectGovernance;

class CreateProject extends CreateRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('projects::filament/resources/project/pages/create-project.notification.title'))
            ->body(__('projects::filament/resources/project/pages/create-project.notification.body'));
    }

    protected function afterCreate(): void
    {
        ProjectGovernance::assertStrategicUnits($this->getRecord());
    }
}
