<?php

use Webkul\Pos\Filament\Resources\PosOrderResource;
use Webkul\Pos\Filament\Resources\PosPaymentMethodResource;
use Webkul\Pos\Filament\Resources\PosRegisterResource;
use Webkul\Pos\Filament\Resources\PosSessionResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];

return [
    'resources' => [
        'manage' => [
            PosRegisterResource::class      => [...$basic, ...$delete],
            PosPaymentMethodResource::class => [...$basic, ...$delete],
            PosSessionResource::class       => [...$basic, ...$delete],
            PosOrderResource::class         => [...$basic, ...$delete],
        ],
        'exclude' => [],
    ],
    'pages' => [
        'exclude' => [],
    ],
];
