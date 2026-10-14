<?php

namespace Webkul\Attendance\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum AttendanceStatus: string implements HasColor, HasLabel
{
    case Present = 'present';

    case Late = 'late';

    case Absent = 'absent';

    case Leave = 'leave';

    public function getLabel(): string
    {
        return __('attendances::enums/attendance-status.'.$this->value);
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Present => 'success',
            self::Late    => 'warning',
            self::Absent  => 'danger',
            self::Leave   => 'info',
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
