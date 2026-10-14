<?php

return [
    'title' => 'Kuantitas',

    'tabs' => [
        'internal-locations' => 'Lokasi Internal',
        'transit-locations'  => 'Lokasi Transit',
        'on-hand'            => 'Di Tangan',
        'to-count'           => 'Untuk Dihitung',
        'to-apply'           => 'Untuk Diterapkan',
    ],

    'form' => [
        'fields' => [
            'product'          => 'Produk',
            'location'         => 'Lokasi',
            'package'          => 'Paket',
            'lot'              => 'Nomor Lot / Seri',
            'on-hand-qty'      => 'Kuantitas di Tangan',
            'storage-category' => 'Kategori Penyimpanan',
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Produk',
            'location'          => 'Lokasi',
            'lot'               => 'Nomor Lot / Seri',
            'storage-category'  => 'Kategori Penyimpanan',
            'quantity'          => 'Kuantitas',
            'package'           => 'Paket',
            'on-hand'           => 'Kuantitas di Tangan',
            'unit'              => 'Satuan',
            'reserved-quantity' => 'Kuantitas Direservasi',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Kuantitas diperbarui',
                    'body'  => 'Kuantitas berhasil diperbarui.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Tambah Kuantitas',

                'notification' => [
                    'title' => 'Kuantitas ditambahkan',
                    'body'  => 'Kuantitas berhasil ditambahkan.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Kuantitas sudah ada',
                        'body'  => 'Sudah ada kuantitas untuk konfigurasi yang sama. Silakan perbarui kuantitas tersebut.',
                    ],
                ],
            ],
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
