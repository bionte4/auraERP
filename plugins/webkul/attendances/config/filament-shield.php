<?php

use Webkul\Attendance\Filament\Resources\AttendanceResource;
use Webkul\Attendance\Filament\Resources\AttendanceSiteResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];

return [
    'resources' => [
        'manage' => [
            AttendanceResource::class     => [...array_diff($basic, ['view']), ...$delete],
            AttendanceSiteResource::class => [...array_diff($basic, ['view']), ...$delete],
        ],
        'exclude' => [],
    ],
    'pages' => [
        'exclude' => [],
    ],
];
