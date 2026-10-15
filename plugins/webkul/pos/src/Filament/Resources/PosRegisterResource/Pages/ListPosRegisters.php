<?php

namespace Webkul\Pos\Filament\Resources\PosRegisterResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Pos\Filament\Resources\PosRegisterResource;

class ListPosRegisters extends ListRecords
{
    protected static string $resource = PosRegisterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
