<?php

namespace Webkul\Performance\Filament\Resources\PerformancePlanResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Performance\Filament\Resources\PerformancePlanResource;

class CreatePerformancePlan extends CreateRecord
{
    protected static string $resource = PerformancePlanResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
