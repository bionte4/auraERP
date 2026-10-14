<?php

namespace Webkul\Attendance\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Attendance\Data\AttendanceLocation;
use Webkul\Attendance\Database\Factories\AttendanceFactory;
use Webkul\Attendance\Enums\AttendanceStatus;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Traits\BelongsToCompany;

class Attendance extends Model
{
    use BelongsToCompany;
    use HasFactory;

    protected $table = 'attendances_attendances';

    protected $fillable = [
        'company_id',
        'employee_id',
        'work_date',
        'check_in',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_accuracy',
        'check_out',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_accuracy',
        'status',
        'note',
    ];

    protected static function newFactory(): AttendanceFactory
    {
        return AttendanceFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'work_date'            => 'date',
            'check_in'             => 'datetime',
            'check_in_latitude'    => 'float',
            'check_in_longitude'   => 'float',
            'check_in_accuracy'    => 'integer',
            'check_out'            => 'datetime',
            'check_out_latitude'   => 'float',
            'check_out_longitude'  => 'float',
            'check_out_accuracy'   => 'integer',
            'status'               => AttendanceStatus::class,
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function tracks(): HasMany
    {
        return $this->hasMany(AttendanceTrack::class);
    }

    public function checkInMapUrl(): ?string
    {
        return $this->mapUrl($this->check_in_latitude, $this->check_in_longitude);
    }

    public function checkOutMapUrl(): ?string
    {
        return $this->mapUrl($this->check_out_latitude, $this->check_out_longitude);
    }

    public function workedLabel(): ?string
    {
        if ($this->check_in === null || $this->check_out === null) {
            return null;
        }

        if ($this->check_out->lessThan($this->check_in)) {
            return null;
        }

        $minutes = (int) $this->check_in->diffInMinutes($this->check_out);
        $hours = intdiv($minutes, 60);
        $remainder = $minutes % 60;

        return sprintf('%d:%02d', $hours, $remainder);
    }

    protected function mapUrl(mixed $latitude, mixed $longitude): ?string
    {
        $location = AttendanceLocation::tryMake($latitude, $longitude);

        return $location?->mapUrl();
    }

    protected static function booted(): void
    {
        static::saving(function (Attendance $attendance): void {
            if (! $attendance->employee_id) {
                return;
            }

            $companyId = Employee::query()->whereKey($attendance->employee_id)->value('company_id');

            if ($companyId) {
                $attendance->company_id = $companyId;
            }
        });
    }
}
