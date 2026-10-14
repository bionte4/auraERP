<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceSiteResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Webkul\Attendance\Filament\Resources\AttendanceSiteResource;

class ManageAttendanceSites extends ManageRecords
{
    protected static string $resource = AttendanceSiteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('attendances::filament/resources/site.actions.create')),
        ];
    }
}
