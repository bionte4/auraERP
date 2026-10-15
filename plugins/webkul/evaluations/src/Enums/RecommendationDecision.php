<?php

namespace Webkul\Evaluation\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RecommendationDecision: string implements HasColor, HasLabel
{
    case AppointPermanent = 'appoint_permanent';

    case ExtendPkwt = 'extend_pkwt';

    case TerminatePkwt = 'terminate_pkwt';

    public function getLabel(): string
    {
        return match ($this) {
            self::AppointPermanent => 'Diangkat Sebagai Karyawan Tetap',
            self::ExtendPkwt       => 'Diperpanjang Masa PKWT',
            self::TerminatePkwt    => 'Tidak Direkomendasikan / Terminasi PKWT',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AppointPermanent => 'success',
            self::ExtendPkwt       => 'warning',
            self::TerminatePkwt    => 'danger',
        };
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $decision): array => [$decision->value => $decision->getLabel()])
            ->all();
    }
}
