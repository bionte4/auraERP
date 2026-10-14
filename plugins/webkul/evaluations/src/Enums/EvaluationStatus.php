<?php

namespace Webkul\Evaluation\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EvaluationStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';

    case InReview = 'in_review';

    case Completed = 'completed';

    public function getLabel(): string
    {
        return __('evaluations::enums/evaluation-status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft     => 'gray',
            self::InReview  => 'warning',
            self::Completed => 'success',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $status): array => [$status->value => $status->getLabel()])
            ->all();
    }
}
