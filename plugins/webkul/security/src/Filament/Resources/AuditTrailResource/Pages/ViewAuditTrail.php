<?php

namespace Webkul\Security\Filament\Resources\AuditTrailResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Security\Filament\Resources\AuditTrailResource;
use Webkul\Security\Support\AdminAccess;

class ViewAuditTrail extends ViewRecord
{
    protected static string $resource = AuditTrailResource::class;

    public function mount(int|string $record): void
    {
        AdminAccess::ensure();

        parent::mount($record);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('security::filament/resources/audit-trail.actions.back'))
                ->url(AuditTrailResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
