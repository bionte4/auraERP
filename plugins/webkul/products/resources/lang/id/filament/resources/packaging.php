<?php

return [
    'form' => [
        'name'    => 'Nama',
        'barcode' => 'Barcode',
        'product' => 'Produk',
        'routes'  => 'Rute',
        'qty'     => 'Kuantitas',
        'company' => 'Perusahaan',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'product'    => 'Produk',
            'routes'     => 'Rute',
            'qty'        => 'Kuantitas',
            'company'    => 'Perusahaan',
            'barcode'    => 'Barcode',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'groups' => [
            'product'    => 'Produk',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'product' => 'Produk',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Pembaruan kemasan',
                    'body'  => 'Kemasan berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kemasan dihapus',
                        'body'  => 'Kemasan berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Kemasan tidak dapat dihapus',
                        'body'  => 'Kemasan tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Cetak',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kemasan dihapus',
                        'body'  => 'Kemasan berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Kemasan tidak dapat dihapus',
                        'body'  => 'Kemasan tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Kemasan Baru',

                'notification' => [
                    'title' => 'Kemasan dibuat',
                    'body'  => 'Kemasan berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'name'    => 'Nama Paket',
                    'barcode' => 'Barcode',
                    'product' => 'Produk',
                    'qty'     => 'Kuantitas',
                ],
            ],

            'organization' => [
                'title' => 'Detail Organisasi',

                'entries' => [
                    'company'    => 'Perusahaan',
                    'creator'    => 'Dibuat Oleh',
                    'created_at' => 'Dibuat Pada',
                    'updated_at' => 'Terakhir Diperbarui Pada',
                ],
            ],
        ],
    ],
];
