<?php

use Webkul\Evaluation\Filament\Resources\EvaluationResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];

return [
    'resources' => [
        'manage' => [
            EvaluationResource::class => [...$basic, ...$delete],
        ],
        'exclude' => [],
    ],
];
