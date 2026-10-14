<?php

namespace Webkul\Attendance\Services;

use Illuminate\Support\Carbon;
use Webkul\Attendance\Data\AttendanceLocation;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\AttendanceTrack;

class AttendanceTracker
{
    public const MIN_INTERVAL_SECONDS = 30;

    public const MAX_POINTS = 2000;

    public function append(Attendance $attendance, AttendanceLocation $location, ?Carbon $at = null, bool $force = false): bool
    {
        if ($attendance->check_in === null || $attendance->check_out !== null) {
            return false;
        }

        $at ??= now();

        if (! $force && ! $this->due($attendance, $at)) {
            return false;
        }

        AttendanceTrack::query()->create([
            'company_id'    => $attendance->company_id,
            'attendance_id' => $attendance->id,
            'recorded_at'   => $at,
            'latitude'      => $location->latitude,
            'longitude'     => $location->longitude,
            'accuracy'      => $location->accuracyMeters,
        ]);

        return true;
    }

    protected function due(Attendance $attendance, Carbon $at): bool
    {
        if ($attendance->tracks()->count() >= self::MAX_POINTS) {
            return false;
        }

        $latest = $attendance->tracks()->orderByDesc('recorded_at')->first();

        if ($latest === null) {
            return true;
        }

        return $latest->recorded_at->diffInSeconds($at, true) >= self::MIN_INTERVAL_SECONDS;
    }
}
