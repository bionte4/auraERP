<?php

use Webkul\Attendance\Filament\Resources\AttendanceResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];

return [
    'resources' => [
        'manage' => [
            AttendanceResource::class => [...array_diff($basic, ['view']), ...$delete],
        ],
        'exclude' => [],
    ],
];
