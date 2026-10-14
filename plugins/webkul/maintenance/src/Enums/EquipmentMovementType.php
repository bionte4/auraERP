<?php

namespace Webkul\Maintenance\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EquipmentMovementType: string implements HasColor, HasLabel
{
    case Assignment = 'assignment';
    case Relocation = 'relocation';
    case Replacement = 'replacement';
    case Return = 'return';
    case Repair = 'repair';
    case Scrap = 'scrap';

    public function getLabel(): string
    {
        return match ($this) {
            self::Assignment  => __('maintenance::enums/equipment-movement-type.assignment'),
            self::Relocation  => __('maintenance::enums/equipment-movement-type.relocation'),
            self::Replacement => __('maintenance::enums/equipment-movement-type.replacement'),
            self::Return      => __('maintenance::enums/equipment-movement-type.return'),
            self::Repair      => __('maintenance::enums/equipment-movement-type.repair'),
            self::Scrap       => __('maintenance::enums/equipment-movement-type.scrap'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Assignment  => 'success',
            self::Relocation  => 'info',
            self::Replacement => 'warning',
            self::Return      => 'gray',
            self::Repair      => 'primary',
            self::Scrap       => 'danger',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getLabel();
        }

        return $options;
    }
}
