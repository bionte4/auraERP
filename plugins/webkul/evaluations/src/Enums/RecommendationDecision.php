<?php

namespace Webkul\Evaluation\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RecommendationDecision: string implements HasColor, HasLabel
{
    case AppointPermanent = 'appoint_permanent';

    case ExtendPkwt = 'extend_pkwt';

    case TransferOuttaskOutsource = 'transfer_outtask_outsource';

    case ExtendOuttaskOutsource = 'extend_outtask_outsource';

    case TerminateOuttaskOutsource = 'terminate_outtask_outsource';

    case TerminatePkwt = 'terminate_pkwt';

    public function getLabel(): string
    {
        return match ($this) {
            self::AppointPermanent          => 'Diangkat Sebagai Karyawan Tetap',
            self::ExtendPkwt                => 'Diperpanjang Masa PKWT',
            self::TransferOuttaskOutsource  => 'Dialihkan ke Outtask / Outsource',
            self::ExtendOuttaskOutsource    => 'Diperpanjang Masa Outtask/Outsource',
            self::TerminateOuttaskOutsource => 'Tidak Diperpanjang Outtask/Outsource',
            self::TerminatePkwt             => 'Tidak Direkomendasikan / Terminasi PKWT',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::AppointPermanent                               => 'success',
            self::ExtendPkwt, self::ExtendOuttaskOutsource       => 'warning',
            self::TransferOuttaskOutsource                       => 'info',
            self::TerminateOuttaskOutsource, self::TerminatePkwt => 'danger',
        };
    }

    public function requiresEffectiveDate(): bool
    {
        return in_array($this, [
            self::AppointPermanent,
            self::TransferOuttaskOutsource,
            self::ExtendOuttaskOutsource,
        ], true);
    }

    public function requiresDurationMonths(): bool
    {
        return in_array($this, [
            self::ExtendPkwt,
            self::TransferOuttaskOutsource,
            self::ExtendOuttaskOutsource,
        ], true);
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
