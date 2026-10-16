<?php

namespace Webkul\Security\Filament\Resources\AuditTrailResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Security\Filament\Resources\AuditTrailResource;
use Webkul\Security\Support\AdminAccess;

class ListAuditTrails extends ListRecords
{
    protected static string $resource = AuditTrailResource::class;

    public function mount(): void
    {
        AdminAccess::ensure();

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
