<?php

namespace Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Chatter\Filament\Actions\ChatterAction;
use Webkul\Maintenance\Filament\Resources\EquipmentResource;

class ViewEquipment extends ViewRecord
{
    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource),
            EditAction::make(),
        ];
    }
}
