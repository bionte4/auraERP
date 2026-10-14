<?php

return [
    'navigation' => [
        'title' => 'Nomor Lot / Seri',
        'group' => 'Persediaan',
    ],

    'global-search' => [
        'ref'     => 'Referensi',
        'product' => 'Produk',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'name'                   => 'Nama',
                    'name-placeholder'       => 'mis. LOT/0001/20121',
                    'product'                => 'Produk',
                    'product-hint-tooltip'   => 'Produk yang terkait dengan nomor lot/seri ini. Produk tidak dapat diubah jika sudah dipindahkan.',
                    'company'                => 'Perusahaan',
                    'reference'              => 'Referensi',
                    'reference-hint-tooltip' => 'Nomor referensi internal, jika berbeda dari nomor lot/seri produsen.',
                    'description'            => 'Deskripsi',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nama',
            'product'      => 'Produk',
            'on-hand-qty'  => 'Kuantitas di Tangan',
            'reference'    => 'Referensi Internal',
            'created-at'   => 'Dibuat Pada',
            'updated-at'   => 'Diperbarui Pada',
        ],

        'groups' => [
            'product'        => 'Produk',
            'location'       => 'Lokasi',
            'created-at'     => 'Dibuat Pada',
        ],

        'filters' => [
            'product'  => 'Produk',
            'location' => 'Lokasi',
            'creator'  => 'Pembuat',
            'company'  => 'Perusahaan',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lot dihapus',
                        'body'  => 'Lot berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Lot tidak dapat dihapus',
                        'body'  => 'Lot tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Cetak Barcode',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lot dihapus',
                        'body'  => 'Lot berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Lot tidak dapat dihapus',
                        'body'  => 'Lot tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Rincian Nomor Lot/Seri',

                'entries' => [
                    'name'        => 'Nama Lot',
                    'product'     => 'Produk',
                    'reference'   => 'Referensi',
                    'description' => 'Deskripsi',
                    'on-hand-qty' => 'Kuantitas di Tangan',
                    'company'     => 'Perusahaan',
                    'created-at'  => 'Dibuat Pada',
                    'updated-at'  => 'Terakhir Diperbarui',
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
