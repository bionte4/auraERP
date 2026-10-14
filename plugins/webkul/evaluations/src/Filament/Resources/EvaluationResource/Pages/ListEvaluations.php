<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;

class ListEvaluations extends ListRecords
{
    protected static string $resource = EvaluationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('evaluations::filament/resources/evaluation/pages/list.header-actions.create'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
