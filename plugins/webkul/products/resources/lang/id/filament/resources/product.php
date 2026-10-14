<?php

return [
    'global-search' => [
        'reference' => 'Referensi',
        'barcode'   => 'Barcode',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'name'             => 'Nama',
                    'name-placeholder' => 'mis. Kaos',
                    'description'      => 'Deskripsi',
                    'tags'             => 'Tag',
                ],
            ],

            'images' => [
                'title' => 'Gambar',
            ],

            'inventory' => [
                'title' => 'Persediaan',

                'fields' => [],

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logistik',

                        'fields' => [
                            'weight' => 'Berat',
                            'volume' => 'Volume',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title' => 'Pengaturan',

                'fields' => [
                    'type'                => 'Tipe',
                    'reference'           => 'Referensi',
                    'barcode'             => 'Barcode',
                    'category'            => 'Kategori',
                    'company'             => 'Perusahaan',
                    'company-placeholder' => 'Semua Perusahaan',
                ],
            ],

            'pricing' => [
                'title' => 'Penetapan Harga',

                'fields' => [
                    'price'           => 'Harga',
                    'cost'            => 'Biaya',
                    'uom-placeholder' => 'UOM',
                ],
            ],

            'additional' => [
                'title' => 'Tambahan',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'            => 'Favorit',
            'name'                => 'Nama',
            'variants'            => 'Varian',
            'images'              => 'Gambar',
            'type'                => 'Tipe',
            'reference'           => 'Referensi',
            'responsible'         => 'Penanggung Jawab',
            'barcode'             => 'Barcode',
            'category'            => 'Kategori',
            'company'             => 'Perusahaan',
            'company-placeholder' => 'Semua Perusahaan',
            'price'               => 'Harga',
            'cost'                => 'Biaya',
            'on-hand'             => 'Di Tangan',
            'tags'                => 'Tag',
            'deleted-at'          => 'Dihapus Pada',
            'created-at'          => 'Dibuat Pada',
            'updated-at'          => 'Diperbarui Pada',
        ],

        'groups' => [
            'type'       => 'Tipe',
            'category'   => 'Kategori',
            'created-at' => 'Dibuat Pada',
        ],

        'filters' => [
            'name'        => 'Nama',
            'type'        => 'Tipe',
            'reference'   => 'Referensi',
            'barcode'     => 'Barcode',
            'category'    => 'Kategori',
            'company'     => 'Perusahaan',
            'price'       => 'Harga',
            'cost'        => 'Biaya',
            'is-favorite' => 'Favorit',
            'weight'      => 'Berat',
            'volume'      => 'Volume',
            'tags'        => 'Tag',
            'responsible' => 'Penanggung Jawab',
            'created-at'  => 'Dibuat Pada',
            'updated-at'  => 'Diperbarui Pada',
            'creator'     => 'Pembuat',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Produk dipulihkan',
                    'body'  => 'Produk berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produk dihapus',
                    'body'  => 'Produk berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Produk dihapus paksa',
                        'body'  => 'Produk berhasil dihapus secara paksa.',
                    ],

                    'error' => [
                        'title' => 'Produk tidak dapat dihapus',
                        'body'  => 'Produk tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Cetak Label',

                'form' => [
                    'fields' => [
                        'quantity' => 'Jumlah Label',
                        'format'   => 'Format',

                        'format-options' => [
                            'dymo'       => 'Dymo',
                            '2x7_price'  => '2x7 dengan harga',
                            '4x7_price'  => '4x7 dengan harga',
                            '4x12'       => '4x12',
                            '4x12_price' => '4x12 dengan harga',
                        ],
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Produk dipulihkan',
                    'body'  => 'Produk berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produk dihapus',
                    'body'  => 'Produk berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Produk dihapus permanen',
                        'body'  => 'Produk berhasil dihapus paksa.',
                    ],

                    'error' => [
                        'title' => 'Produk tidak dapat dihapus',
                        'body'  => 'Produk tidak dapat dihapus karena sedang digunakan.',
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
                    'name'             => 'Nama',
                    'name-placeholder' => 'mis. Kaos',
                    'description'      => 'Deskripsi',
                    'tags'             => 'Tag',
                ],
            ],

            'images' => [
                'title' => 'Gambar',

                'entries' => [],
            ],

            'settings' => [
                'title' => 'Pengaturan',

                'entries' => [
                    'type'      => 'Tipe',
                    'reference' => 'Referensi',
                    'barcode'   => 'Barcode',
                    'category'  => 'Kategori',
                    'company'   => 'Perusahaan',
                ],
            ],

            'pricing' => [
                'title' => 'Penetapan Harga',

                'entries' => [
                    'price' => 'Harga',
                    'cost'  => 'Biaya',
                ],
            ],

            'inventory' => [
                'title' => 'Persediaan',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logistik',

                        'entries' => [
                            'weight' => 'Berat',
                            'volume' => 'Volume',
                        ],
                    ],
                ],
            ],

            'record-information' => [
                'title' => 'Informasi Rekaman',

                'entries' => [
                    'created-at' => 'Dibuat Pada',
                    'created-by' => 'Dibuat Oleh',
                    'updated-at' => 'Diperbarui Pada',
                ],
            ],
        ],
    ],
];
