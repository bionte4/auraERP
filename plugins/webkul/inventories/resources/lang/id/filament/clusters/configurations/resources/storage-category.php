<?php

return [
    'navigation' => [
        'title' => 'Kategori Penyimpanan',
        'group' => 'Manajemen Gudang',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',

                'fields' => [
                    'name'               => 'Nama',
                    'allow-new-products' => 'Izinkan Produk Baru',
                    'max-weight'         => 'Berat Maksimum',
                    'company'            => 'Perusahaan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nama',
            'allow-new-products' => 'Izinkan Produk Baru',
            'max-weight'         => 'Berat Maksimum',
            'company'            => 'Perusahaan',
            'deleted-at'         => 'Dihapus Pada',
            'created-at'         => 'Dibuat Pada',
            'updated-at'         => 'Diperbarui Pada',
        ],

        'groups' => [
            'allow-new-products' => 'Izinkan Produk Baru',
            'created-at'         => 'Dibuat Pada',
            'updated-at'         => 'Diperbarui Pada',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kategori Penyimpanan dihapus',
                    'body'  => 'Kategori penyimpanan berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kategori Penyimpanan dihapus',
                    'body'  => 'Kategori penyimpanan berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',

                'entries' => [
                    'name'               => 'Nama',
                    'allow-new-products' => 'Izinkan Produk Baru',
                    'max-weight'         => 'Berat Maksimum',
                    'company'            => 'Perusahaan',
                ],
            ],

            'record-information' => [
                'title' => 'Informasi Rekaman',

                'entries' => [
                    'created-by'   => 'Dibuat Oleh',
                    'created-at'   => 'Dibuat Pada',
                    'last-updated' => 'Terakhir Diperbarui',
                ],
            ],
        ],
    ],
];
