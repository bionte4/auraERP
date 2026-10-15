<?php

namespace Webkul\Evaluation\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum EvaluationType: string implements HasColor, HasLabel
{
    case PkwtQ1 = 'pkwt_q1';

    case PkwtQ2 = 'pkwt_q2';

    case PkwtQ3 = 'pkwt_q3';

    case PkwtFinal = 'pkwt_final';

    case OutsourceOuttask = 'outsource_outtask';

    case Regular = 'regular';

    public function getLabel(): string
    {
        return match ($this) {
            self::PkwtQ1           => 'Triwulan I PKWT',
            self::PkwtQ2           => 'Triwulan II PKWT',
            self::PkwtQ3           => 'Triwulan III PKWT',
            self::PkwtFinal        => 'Evaluasi Akhir PKWT (Keputusan Pengangkatan)',
            self::OutsourceOuttask => 'Evaluasi Karyawan Outtask / Outsource',
            self::Regular          => 'Evaluasi Berkala / Reguler',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PkwtQ1, self::PkwtQ2, self::PkwtQ3 => 'info',
            self::PkwtFinal                          => 'warning',
            self::OutsourceOuttask                   => 'gray',
            self::Regular                            => 'primary',
        };
    }

    public function isPkwt(): bool
    {
        return in_array($this, [self::PkwtQ1, self::PkwtQ2, self::PkwtQ3, self::PkwtFinal], true);
    }

    public function isOutsourceOrOuttask(): bool
    {
        return $this === self::OutsourceOuttask;
    }

    /**
     * @return array<string, string>
     */
    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $type): array => [$type->value => $type->getLabel()])
            ->all();
    }
}
