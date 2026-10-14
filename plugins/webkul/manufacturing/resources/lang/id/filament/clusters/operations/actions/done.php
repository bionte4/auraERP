<?php

return [
    'label'         => 'Produksi Semua',
    'partial-label' => 'Produksi',

    'modal' => [
        'consumption-warning' => [
            'heading'     => 'Peringatan Konsumsi',
            'description' => 'Beberapa produk mengonsumsi kuantitas yang berbeda dari yang diharapkan. Apakah Anda ingin memvalidasi perintah manufaktur dengan kuantitas saat ini?',

            'form' => [
                'product'    => 'Produk',
                'to-consume' => 'Untuk Dikonsumsi',
                'consumed'   => 'Dikonsumsi',
                'uom'        => 'Satuan ukur',
            ],

            'actions' => [
                'confirm' => [
                    'label' => 'Konfirmasi',
                ],

                'set-quantities' => [
                    'label' => 'Tetapkan Kuantitas dan Konfirmasi',
                ],
            ],
        ],

        'produced-warning' => [
            'heading'     => 'Hasil produksi berbeda dari yang diharapkan',
            'description' => 'Kuantitas yang diproduksi berbeda dari yang diharapkan. Apakah Anda ingin mengonfirmasi perintah manufaktur dengan kuantitas saat ini?',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Pesanan manufaktur selesai',
            'body'  => 'Perintah manufaktur berhasil diselesaikan.',
        ],
    ],
];
