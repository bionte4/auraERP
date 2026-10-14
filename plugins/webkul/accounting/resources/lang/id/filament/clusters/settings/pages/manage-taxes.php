<?php

return [
    'title' => 'Kelola Pajak',

    'form' => [
        'default-taxes' => [
            'label'       => 'Pajak Bawaan',
            'helper-text' => 'Nilai bawaan akan diterapkan pada produk jika tidak ada pajak yang dipilih',
        ],

        'sales-tax' => [
            'label' => 'Pajak Penjualan',
        ],

        'purchase-tax' => [
            'label' => 'Pajak Pembelian',
        ],

        'prices' => [
            'label' => 'Harga',
        ],

        'rounding-method' => [
            'label'       => 'Metode Pembulatan',
            'helper-text' => 'Metode pembulatan jumlah pajak',

            'options' => [
                'round-per-line' => 'Bulatkan per Baris',
                'round-globally' => 'Bulatkan secara Global',
            ],
        ],

        'fiscal-country' => [
            'label' => 'Negara Fiskal',
        ],
    ],
];
