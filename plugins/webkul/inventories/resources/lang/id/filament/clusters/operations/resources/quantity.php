<?php

return [
    'navigation' => [
        'title' => 'Kuantitas',
        'group' => 'Penyesuaian',
    ],

    'form' => [
        'fields' => [
            'location'         => 'Lokasi',
            'product'          => 'Produk',
            'package'          => 'Paket',
            'lot'              => 'Nomor Lot / Seri',
            'counted-qty'      => 'Kuantitas Terhitung',
            'scheduled-at'     => 'Dijadwalkan Pada',
            'storage-category' => 'Kategori Penyimpanan',
        ],
    ],

    'table' => [
        'columns' => [
            'location'           => 'Lokasi',
            'product'            => 'Produk',
            'product-category'   => 'Kategori Produk',
            'lot'                => 'Nomor Lot / Seri',
            'storage-category'   => 'Kategori Penyimpanan',
            'available-quantity' => 'Kuantitas Tersedia',
            'quantity'           => 'Kuantitas',
            'package'            => 'Paket',
            'last-counted-at'    => 'Terakhir Dihitung Pada',
            'on-hand'            => 'Kuantitas di Tangan',
            'uom'                => 'UOM',
            'counted'            => 'Kuantitas Terhitung',
            'difference'         => 'Selisih',
            'scheduled-at'       => 'Dijadwalkan Pada',
            'user'               => 'Pengguna',
            'company'            => 'Perusahaan',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Kuantitas diperbarui',
                    'body'  => 'Kuantitas berhasil diperbarui.',
                ],
            ],
        ],

        'groups' => [
            'product'          => 'Produk',
            'product-category' => 'Kategori Produk',
            'location'         => 'Lokasi',
            'storage-category' => 'Kategori Penyimpanan',
            'lot'              => 'Nomor Lot / Seri',
            'company'          => 'Perusahaan',
            'package'          => 'Paket',
        ],

        'filters' => [
            'product'             => 'Produk',
            'uom'                 => 'Satuan ukur',
            'product-category'    => 'Kategori Produk',
            'location'            => 'Lokasi',
            'storage-category'    => 'Kategori Penyimpanan',
            'lot'                 => 'Nomor Lot / Seri',
            'company'             => 'Perusahaan',
            'package'             => 'Paket',
            'on-hand-quantity'    => 'Kuantitas di Tangan',
            'difference-quantity' => 'Kuantitas Selisih',
            'incoming-at'         => 'Masuk Pada',
            'scheduled-at'        => 'Dijadwalkan Pada',
            'user'                => 'Pengguna',
            'created-at'          => 'Dibuat Pada',
            'updated-at'          => 'Diperbarui Pada',
            'company'             => 'Perusahaan',
            'creator'             => 'Pembuat',
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
                        'body'  => 'Kuantitas sudah ada untuk konfigurasi ini. Silakan perbarui kuantitas yang sudah ada.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'apply' => [
                'label' => 'Terapkan',

                'notification' => [
                    'title' => 'Perubahan kuantitas diterapkan',
                    'body'  => 'Perubahan kuantitas berhasil diterapkan.',
                ],
            ],

            'clear' => [
                'label' => 'Bersihkan',

                'notification' => [
                    'title' => 'Perubahan kuantitas dibersihkan',
                    'body'  => 'Perubahan kuantitas berhasil dikosongkan.',
                ],
            ],
        ],
    ],
];
