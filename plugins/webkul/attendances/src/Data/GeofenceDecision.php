<?php

namespace Webkul\Attendance\Data;

use Webkul\Attendance\Models\AttendanceSite;

class GeofenceDecision
{
    public function __construct(
        public bool $allowed,
        public ?AttendanceSite $site,
    ) {}
}
