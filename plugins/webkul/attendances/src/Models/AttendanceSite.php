<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Attendance\Data\AttendanceLocation;
use Webkul\Support\Traits\BelongsToCompany;

class AttendanceSite extends Model
{
    use BelongsToCompany;

    protected $table = 'attendances_sites';

    protected $fillable = [
        'company_id',
        'name',
        'latitude',
        'longitude',
        'radius_meters',
        'is_active',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude'      => 'float',
            'longitude'     => 'float',
            'radius_meters' => 'integer',
            'is_active'     => 'boolean',
        ];
    }

    public function contains(AttendanceLocation $location): bool
    {
        return $this->distanceMeters($location) <= $this->radius_meters;
    }

    public function distanceMeters(AttendanceLocation $location): float
    {
        $earth = 6371000;
        $latFrom = deg2rad((float) $this->latitude);
        $latTo = deg2rad($location->latitude);
        $latDelta = deg2rad($location->latitude - (float) $this->latitude);
        $lngDelta = deg2rad($location->longitude - (float) $this->longitude);
        $a = sin($latDelta / 2) ** 2 + cos($latFrom) * cos($latTo) * sin($lngDelta / 2) ** 2;

        return 2 * $earth * asin(min(1, sqrt($a)));
    }

    public function mapUrl(): string
    {
        return 'https://www.openstreetmap.org/?mlat='.$this->latitude.'&mlon='.$this->longitude.'#map=18/'.$this->latitude.'/'.$this->longitude;
    }

    public function checkIns(): HasMany
    {
        return $this->hasMany(Attendance::class, 'check_in_site_id');
    }

    public function checkOuts(): HasMany
    {
        return $this->hasMany(Attendance::class, 'check_out_site_id');
    }
}
