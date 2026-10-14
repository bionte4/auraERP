<?php

return [
    'title' => 'Lokasi',

    'table' => [
        'columns' => [
            'product'          => 'Produk',
            'location'         => 'Lokasi',
            'storage-category' => 'Kategori Penyimpanan',
            'quantity'         => 'Kuantitas',
            'package'          => 'Paket',
            'on-hand'          => 'Kuantitas di Tangan',
            'unit'             => 'Satuan',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kuantitas dihapus',
                    'body'  => 'Kuantitas berhasil dihapus.',
                ],
            ],
        ],
    ],
];
