<?php

namespace Webkul\Performance\Filament\Resources\PerformancePlanResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\Performance\Filament\Resources\PerformancePlanResource;

class EditPerformancePlan extends EditRecord
{
    protected static string $resource = PerformancePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
