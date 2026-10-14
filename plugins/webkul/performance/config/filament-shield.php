<?php

use Webkul\Performance\Filament\Resources\PerformancePlanResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];

return [
    'resources' => [
        'manage' => [
            PerformancePlanResource::class => [...$basic, ...$delete],
        ],
        'exclude' => [],
    ],
];
