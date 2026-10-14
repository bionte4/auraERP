<?php

return [
    'title' => 'MASUK/KELUAR',

    'tabs' => [
        'todo'     => 'Akan Dikerjakan',
        'done'     => 'Selesai',
        'incoming' => 'Masuk',
        'outgoing' => 'Keluar',
        'internal' => 'Internal',
    ],

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
