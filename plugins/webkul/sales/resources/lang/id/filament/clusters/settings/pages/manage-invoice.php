<?php

return [
    'title' => 'Kelola Faktur',

    'breadcrumb' => 'Kelola Faktur',

    'navigation' => [
        'title' => 'Kelola Faktur',
    ],

    'form' => [
        'invoice-policy' => [
            'label'      => 'Kebijakan Faktur',
            'label-help' => 'Tentukan cara faktur dibuat dari pesanan penjualan.',
            'options'    => [
                'order'    => 'Buat faktur berdasarkan kuantitas yang dipesan',
                'delivery' => 'Buat faktur berdasarkan kuantitas yang dikirim',
            ],
        ],
    ],
];
