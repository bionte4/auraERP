<?php

namespace Webkul\Pos\Filament\Resources\PosRegisterResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Webkul\Pos\Filament\Resources\PosRegisterResource;

class EditPosRegister extends EditRecord
{
    protected static string $resource = PosRegisterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
