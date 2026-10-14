<?php

return [
    'title' => 'Kapasitas Berdasarkan Produk',

    'form' => [
        'product' => 'Produk',
        'qty'     => 'Kuantitas',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Tambah Kapasitas Produk',

                'notification' => [
                    'title' => 'Kapasitas Produk dibuat',
                    'body'  => 'Kapasitas produk berhasil ditambahkan.',
                ],
            ],
        ],

        'columns' => [
            'product' => 'Produk',
            'qty'     => 'Kuantitas',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kapasitas Produk diperbarui',
                    'body'  => 'Kapasitas produk berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kapasitas Produk dihapus',
                    'body'  => 'Kapasitas produk berhasil dihapus.',
                ],
            ],
        ],
    ],
];
