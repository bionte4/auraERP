<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Filament\Resources\EvaluationResource;
use Webkul\Evaluation\Models\Evaluation;

class CreateEvaluation extends CreateRecord
{
    protected static string $resource = EvaluationResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = EvaluationStatus::InReview->value;

        return $data;
    }

    protected function afterCreate(): void
    {
        /** @var Evaluation $record */
        $record = $this->getRecord();

        if ($record->status === EvaluationStatus::Draft) {
            $record->status = EvaluationStatus::InReview;
            $record->saveQuietly();
        }

        if ($record->ratings()->count() === 0) {
            $record->seedDefaultPkwtCriteria();
        }

        if ($record->goal_achievement_percentage === null) {
            $record->syncGoalFromPerformancePlan();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->getRecord()]);
    }
}
