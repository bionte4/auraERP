<?php

namespace Webkul\Performance\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum GoalStatus: string implements HasColor, HasLabel
{
    case NotStarted = 'not_started';

    case InProgress = 'in_progress';

    case Achieved = 'achieved';

    case Missed = 'missed';

    public function getLabel(): string
    {
        return __('performance::enums/goal-status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::NotStarted => 'gray',
            self::InProgress => 'warning',
            self::Achieved   => 'success',
            self::Missed     => 'danger',
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
