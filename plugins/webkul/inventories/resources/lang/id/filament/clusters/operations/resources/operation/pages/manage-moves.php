<?php

return [
    'title' => 'Perpindahan',

    'table' => [
        'columns' => [
            'date'                 => 'Tanggal',
            'reference'            => 'Referensi',
            'product'              => 'Produk',
            'package'              => 'Paket',
            'lot'                  => 'Nomor Lot / Seri',
            'source-location'      => 'Lokasi Sumber',
            'destination-location' => 'Lokasi Tujuan',
            'quantity'             => 'Kuantitas',
            'unit'                 => 'Satuan',
            'state'                => 'Status',
            'done-by'              => 'Diselesaikan Oleh',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pergerakan dihapus',
                    'body'  => 'Pergerakan berhasil dihapus.',
                ],
            ],
        ],
    ],
];
