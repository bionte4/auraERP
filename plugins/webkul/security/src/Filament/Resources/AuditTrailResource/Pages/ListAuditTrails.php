<?php

namespace Webkul\Security\Filament\Resources\AuditTrailResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Security\Filament\Resources\AuditTrailResource;

class ListAuditTrails extends ListRecords
{
    protected static string $resource = AuditTrailResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
