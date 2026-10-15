<?php

namespace Webkul\Pos\Filament\Resources\PosPaymentMethodResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\Pos\Filament\Resources\PosPaymentMethodResource;

class EditPosPaymentMethod extends EditRecord
{
    protected static string $resource = PosPaymentMethodResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
