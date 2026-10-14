<?php

namespace Webkul\Account\Filament\Resources\CustomerResource\Pages;

use Webkul\Account\Filament\Resources\CustomerResource;
use Webkul\Account\Filament\Resources\PartnerResource\Pages\ListPartners;

class ListCustomers extends ListPartners
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ...parent::getHeaderActions(),
        ];
    }

    public function getPresetTableViews(): array
    {
        $views = parent::getPresetTableViews();

        unset($views['employees'], $views['customers'], $views['vendors']);

        return $views;
    }
}
