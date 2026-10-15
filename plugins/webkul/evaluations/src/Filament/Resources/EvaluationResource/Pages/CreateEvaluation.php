<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;
use Webkul\Evaluation\Models\Evaluation;

class CreateEvaluation extends CreateRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function afterCreate(): void
    {
        /** @var Evaluation $record */
        $record = $this->getRecord();

        if ($record->goal_achievement_percentage === null) {
            $record->syncGoalFromPerformancePlan();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
