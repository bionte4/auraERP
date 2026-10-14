<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;

class CreateEvaluation extends CreateRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
