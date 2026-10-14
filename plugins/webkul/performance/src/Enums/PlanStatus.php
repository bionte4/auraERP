<?php

namespace Webkul\Performance\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum PlanStatus: string implements HasColor, HasLabel
{
    case Draft = 'draft';

    case Active = 'active';

    case Closed = 'closed';

    public function getLabel(): string
    {
        return __('performance::enums/plan-status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Draft  => 'gray',
            self::Active => 'success',
            self::Closed => 'info',
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
