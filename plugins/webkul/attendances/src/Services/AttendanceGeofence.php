<?php

namespace Webkul\Attendance\Services;

use Webkul\Attendance\Data\AttendanceLocation;
use Webkul\Attendance\Data\GeofenceDecision;
use Webkul\Attendance\Models\AttendanceSite;

class AttendanceGeofence
{
    public function decide(AttendanceLocation $location): GeofenceDecision
    {
        $sites = AttendanceSite::query()->where('is_active', true)->get();

        if ($sites->isEmpty()) {
            return new GeofenceDecision(allowed: true, site: null);
        }

        $match = $sites->first(fn (AttendanceSite $site): bool => $site->contains($location));

        return new GeofenceDecision(allowed: $match !== null, site: $match);
    }
}
