<?php

return [
    'navigation' => [
        'title' => 'Perjanjian Pembelian',
        'group' => 'Pembelian',
    ],

    'global-search' => [
        'vendor' => 'Pemasok',
        'type'   => 'Tipe',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'vendor'                => 'Pemasok',
                    'valid-from'            => 'Berlaku Dari',
                    'valid-to'              => 'Berlaku Hingga',
                    'buyer'                 => 'Pembeli',
                    'reference'             => 'Referensi',
                    'reference-placeholder' => 'mis. PO/123',
                    'agreement-type'        => 'Tipe Perjanjian',
                    'company'               => 'Perusahaan',
                    'currency'              => 'Mata Uang',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produk',

                'columns' => [
                    'product'    => 'Produk',
                    'quantity'   => 'Kuantitas',
                    'ordered'    => 'Dipesan',
                    'uom'        => 'Satuan ukur',
                    'unit-price' => 'Harga Satuan',
                ],

                'fields' => [
                    'product'    => 'Produk',
                    'quantity'   => 'Kuantitas',
                    'ordered'    => 'Dipesan',
                    'uom'        => 'Satuan ukur',
                    'unit-price' => 'Harga Satuan',
                ],
            ],

            'additional' => [
                'title' => 'Informasi Tambahan',
            ],

            'terms' => [
                'title' => 'Syarat dan Ketentuan',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'agreement'      => 'Perjanjian',
            'vendor'         => 'Pemasok',
            'agreement-type' => 'Tipe Perjanjian',
            'buyer'          => 'Pembeli',
            'company'        => 'Perusahaan',
            'valid-from'     => 'Berlaku Dari',
            'valid-to'       => 'Berlaku Hingga',
            'reference'      => 'Referensi',
            'status'         => 'Status',
        ],

        'groups' => [
            'agreement-type' => 'Tipe Perjanjian',
            'vendor'         => 'Pemasok',
            'state'          => 'Status',
            'created-at'     => 'Dibuat Pada',
            'updated-at'     => 'Diperbarui Pada',
        ],

        'filters' => [
            'agreement'      => 'Perjanjian',
            'vendor'         => 'Pemasok',
            'agreement-type' => 'Tipe Perjanjian',
            'buyer'          => 'Pembeli',
            'company'        => 'Perusahaan',
            'valid-from'     => 'Berlaku Dari',
            'valid-to'       => 'Berlaku Hingga',
            'reference'      => 'Referensi',
            'status'         => 'Status',
            'created-at'     => 'Dibuat Pada',
            'updated-at'     => 'Diperbarui Pada',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Perjanjian Pembelian dihapus',
                    'body'  => 'Perjanjian pembelian berhasil dihapus.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Perjanjian Pembelian dipulihkan',
                    'body'  => 'Perjanjian pembelian berhasil dipulihkan.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Perjanjian pembelian dihapus secara permanen',
                        'body'  => 'Perjanjian pembelian berhasil dihapus secara permanen.',
                    ],

                    'error' => [
                        'title' => 'Perjanjian pembelian tidak dapat dihapus',
                        'body'  => 'Perjanjian pembelian tidak dapat dihapus karena sedang digunakan.',
                    ],

                    'warning' => [
                        'title' => 'Perjanjian pembelian tidak dapat dihapus',
                        'body'  => 'Hanya perjanjian pembelian dalam status Draf atau Dibatalkan yang dapat dihapus.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Perjanjian Pembelian dihapus',
                    'body'  => 'Perjanjian pembelian berhasil dihapus.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Perjanjian Pembelian dipulihkan',
                    'body'  => 'Perjanjian pembelian berhasil dipulihkan.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Perjanjian pembelian dihapus secara permanen',
                        'body'  => 'Perjanjian pembelian berhasil dihapus secara permanen.',
                    ],

                    'error' => [
                        'title' => 'Perjanjian pembelian tidak dapat dihapus',
                        'body'  => 'Perjanjian pembelian tidak dapat dihapus karena sedang digunakan.',
                    ],

                    'warning' => [
                        'title' => 'Perjanjian pembelian tidak dapat dihapus',
                        'body'  => 'Hanya perjanjian pembelian dalam status Draf atau Dibatalkan yang dapat dihapus.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'entries' => [
                    'vendor'                => 'Pemasok',
                    'valid-from'            => 'Berlaku Dari',
                    'valid-to'              => 'Berlaku Hingga',
                    'buyer'                 => 'Pembeli',
                    'reference'             => 'Referensi',
                    'reference-placeholder' => 'mis. PO/123',
                    'agreement-type'        => 'Tipe Perjanjian',
                    'company'               => 'Perusahaan',
                    'currency'              => 'Mata Uang',
                ],
            ],

            'metadata' => [
                'title' => 'Metadata',

                'entries' => [
                    'created-at' => 'Dibuat Pada',
                    'created-by' => 'Dibuat Oleh',
                    'updated-at' => 'Diperbarui Pada',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produk',

                'entries' => [
                    'product'    => 'Produk',
                    'quantity'   => 'Kuantitas',
                    'ordered'    => 'Dipesan',
                    'uom'        => 'Satuan ukur',
                    'unit-price' => 'Harga Satuan',
                ],
            ],

            'additional' => [
                'title' => 'Informasi Tambahan',
            ],

            'terms' => [
                'title' => 'Syarat dan Ketentuan',
            ],
        ],
    ],
];
