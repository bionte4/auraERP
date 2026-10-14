<?php

return [
    'navigation' => [
        'title' => 'Rute',
        'group' => 'Manajemen Gudang',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',

                'fields' => [
                    'route'             => 'Rute',
                    'route-placeholder' => 'mis. Penerimaan Dua Langkah',
                    'company'           => 'Perusahaan',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Berlaku Pada',
                'description' => 'Pilih lokasi tempat rute ini dapat diterapkan.',

                'fields' => [
                    'products'                        => 'Produk',
                    'products-hint-tooltip'           => 'Jika dipilih, rute ini akan tersedia untuk dipilih pada produk.',
                    'product-categories'              => 'Kategori Produk',
                    'product-categories-hint-tooltip' => 'Jika dipilih, rute ini akan tersedia untuk dipilih pada kategori produk.',
                    'warehouses'                      => 'Gudang',
                    'warehouses-hint-tooltip'         => 'Ketika sebuah gudang ditetapkan pada rute ini, rute tersebut akan dianggap sebagai rute bawaan untuk produk yang bergerak melalui gudang itu.',
                    'packaging'                       => 'Kemasan',
                    'packaging-hint-tooltip'          => 'Jika dipilih, rute ini akan tersedia untuk dipilih pada kemasan.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'route'      => 'Rute',
            'company'    => 'Perusahaan',
            'deleted-at' => 'Dihapus Pada',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'groups' => [
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'company'  => 'Perusahaan',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Rute diperbarui',
                    'body'  => 'Rute berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Rute dipulihkan',
                    'body'  => 'Rute berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rute dihapus',
                    'body'  => 'Rute berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rute dihapus paksa',
                        'body'  => 'Rute berhasil dihapus secara paksa.',
                    ],

                    'error' => [
                        'title' => 'Rute tidak dapat dihapus',
                        'body'  => 'Rute tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Rute dipulihkan',
                    'body'  => 'Rute berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rute dihapus',
                    'body'  => 'Rute berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rute dihapus paksa',
                        'body'  => 'Rute berhasil dihapus secara paksa.',
                    ],

                    'error' => [
                        'title' => 'Rute tidak dapat dihapus',
                        'body'  => 'Rute tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',

                'entries' => [
                    'route'             => 'Rute',
                    'route-placeholder' => 'mis. Penerimaan Dua Langkah',
                    'company'           => 'Perusahaan',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Berlaku Pada',
                'description' => 'Pilih tempat di mana rute ini dapat dipilih.',

                'entries' => [
                    'products'                        => 'Produk',
                    'products-hint-tooltip'           => 'Jika dipilih, rute ini akan tersedia untuk dipilih pada produk.',
                    'product-categories'              => 'Kategori Produk',
                    'product-categories-hint-tooltip' => 'Jika dipilih, rute ini akan tersedia untuk dipilih pada kategori produk.',
                    'warehouses'                      => 'Gudang',
                    'warehouses-hint-tooltip'         => 'Ketika sebuah gudang ditetapkan pada rute ini, rute tersebut akan dianggap sebagai rute bawaan untuk produk yang bergerak melalui gudang itu.',
                    'packaging'                       => 'Kemasan',
                    'packaging-hint-tooltip'          => 'Jika dipilih, rute ini akan tersedia untuk dipilih pada kemasan.',
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
