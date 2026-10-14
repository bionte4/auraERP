<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Attendance\Data\AttendanceLocation;
use Webkul\Support\Traits\BelongsToCompany;

class AttendanceTrack extends Model
{
    use BelongsToCompany;

    protected $table = 'attendances_tracks';

    protected $fillable = [
        'company_id',
        'attendance_id',
        'recorded_at',
        'latitude',
        'longitude',
        'accuracy',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'recorded_at' => 'datetime',
            'latitude'    => 'float',
            'longitude'   => 'float',
            'accuracy'    => 'integer',
        ];
    }

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function mapUrl(): ?string
    {
        return AttendanceLocation::tryMake($this->latitude, $this->longitude)?->mapUrl();
    }

    protected static function booted(): void
    {
        static::saving(function (AttendanceTrack $track): void {
            if ($track->company_id || ! $track->attendance_id) {
                return;
            }

            $companyId = Attendance::query()->whereKey($track->attendance_id)->value('company_id');

            if ($companyId) {
                $track->company_id = $companyId;
            }
        });
    }
}
