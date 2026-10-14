<?php

namespace Webkul\Performance\Filament\Resources\PerformancePlanResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Performance\Filament\Resources\PerformancePlanResource;

class ListPerformancePlans extends ListRecords
{
    protected static string $resource = PerformancePlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('performance::filament/resources/performance-plan/pages/list.header-actions.create'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
