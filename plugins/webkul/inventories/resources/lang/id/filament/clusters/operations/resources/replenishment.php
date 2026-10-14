<?php

return [
    'navigation' => [
        'title' => 'Pengisian Ulang',
        'group' => 'Pengadaan',
    ],

    'form' => [
        'fields' => [
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Produk',
            'location'          => 'Lokasi',
            'route'             => 'Rute',
            'vendor'            => 'Pemasok',
            'trigger'           => 'Pemicu',
            'on-hand'           => 'Di Tangan',
            'min'               => 'Min',
            'max'               => 'Maks',
            'multiple-quantity' => 'Kuantitas Kelipatan',
            'to-order'          => 'Untuk Dipesan',
            'uom'               => 'UOM',
            'company'           => 'Perusahaan',
        ],

        'groups' => [
            'location' => 'Lokasi',
            'product'  => 'Produk',
            'category' => 'Kategori',
        ],

        'filters' => [
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Tambah Pengisian Ulang',

                'notification' => [
                    'title' => 'Pengisian ulang ditambahkan',
                    'body'  => 'Pengisian kembali berhasil ditambahkan.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Pengisian ulang sudah ada',
                        'body'  => 'Pengisian ulang sudah ada untuk konfigurasi ini. Silakan perbarui pengisian ulang yang sudah ada.',
                    ],
                ],
            ],
        ],

        'actions' => [
        ],
    ],
];
