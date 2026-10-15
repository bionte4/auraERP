<?php

namespace Webkul\Pos\Filament\Resources\PosPaymentMethodResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Pos\Filament\Resources\PosPaymentMethodResource;

class ListPosPaymentMethods extends ListRecords
{
    protected static string $resource = PosPaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
