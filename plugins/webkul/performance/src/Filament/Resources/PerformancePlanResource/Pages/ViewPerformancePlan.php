<?php

namespace Webkul\Performance\Filament\Resources\PerformancePlanResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Performance\Filament\Resources\PerformancePlanResource;

class ViewPerformancePlan extends ViewRecord
{
    protected static string $resource = PerformancePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
