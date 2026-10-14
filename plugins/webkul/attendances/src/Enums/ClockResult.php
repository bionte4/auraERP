<?php

namespace Webkul\Attendance\Enums;

enum ClockResult: string
{
    case Recorded = 'recorded';

    case AlreadyClockedIn = 'already_clocked_in';

    case MissingClockIn = 'missing_clock_in';
}
