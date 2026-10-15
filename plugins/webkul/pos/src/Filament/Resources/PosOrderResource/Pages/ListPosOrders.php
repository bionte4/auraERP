<?php

namespace Webkul\Pos\Filament\Resources\PosOrderResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;
use Webkul\Pos\Filament\Pages\PosTerminalPage;
use Webkul\Pos\Filament\Resources\PosOrderResource;

class ListPosOrders extends ListRecords
{
    protected static string $resource = PosOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('go_to_terminal')
                ->label('Buka Layar Kasir (POS)')
                ->icon('heroicon-o-calculator')
                ->color('primary')
                ->url(fn (): string => PosTerminalPage::getUrl()),
        ];
    }
}
