<?php

namespace Webkul\Attendance\Data;

class AttendanceLocation
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public ?int $accuracyMeters,
    ) {}

    public static function tryMake(mixed $latitude, mixed $longitude, mixed $accuracy = null): ?self
    {
        if (! is_numeric($latitude) || ! is_numeric($longitude)) {
            return null;
        }

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        if ($latitude < -90 || $latitude > 90 || $longitude < -180 || $longitude > 180) {
            return null;
        }

        $accuracyMeters = null;

        if (is_numeric($accuracy)) {
            $accuracyMeters = max(0, (int) round((float) $accuracy));
        }

        return new self($latitude, $longitude, $accuracyMeters);
    }

    public function label(): string
    {
        return number_format($this->latitude, 5).', '.number_format($this->longitude, 5);
    }

    public function mapUrl(): string
    {
        return 'https://www.openstreetmap.org/?mlat='.$this->latitude.'&mlon='.$this->longitude.'#map=18/'.$this->latitude.'/'.$this->longitude;
    }
}
