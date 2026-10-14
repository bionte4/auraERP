<?php

return [
    'navigation' => [
        'title' => 'Produk',
        'group' => 'Persediaan',
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
                    'sales'            => 'Penjualan',
                    'purchase'         => 'Pembelian',
                ],
            ],

            'invoice-policy' => [
                'title'            => 'Kebijakan Faktur',
                'ordered-policy'   => 'Anda dapat membuat faktur untuk barang sebelum dikirim.',
                'delivered-policy' => 'Buat faktur setelah pengiriman, berdasarkan kuantitas yang dikirim, bukan yang dipesan.',
            ],

            'images' => [
                'title' => 'Gambar',
            ],

            'settings' => [
                'title' => 'Pengaturan',

                'fields' => [
                    'type'      => 'Tipe',
                    'reference' => 'Referensi',
                    'barcode'   => 'Barcode',
                    'category'  => 'Kategori',
                    'company'   => 'Perusahaan',
                ],
            ],

            'category-and-tags' => [
                'title' => 'Kategori & Tag',

                'fields' => [
                    'category' => 'Kategori',
                    'tags'     => 'Tag',
                ],
            ],

            'pricing' => [
                'title' => 'Penetapan Harga',

                'fields' => [
                    'price' => 'Harga',
                    'cost'  => 'Biaya',
                ],
            ],

            'additional' => [
                'title' => 'Tambahan',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nama',
            'images'      => 'Gambar',
            'type'        => 'Tipe',
            'reference'   => 'Referensi',
            'responsible' => 'Penanggung Jawab',
            'barcode'     => 'Barcode',
            'category'    => 'Kategori',
            'company'     => 'Perusahaan',
            'price'       => 'Harga',
            'cost'        => 'Biaya',
            'tags'        => 'Tag',
            'deleted-at'  => 'Dihapus Pada',
            'created-at'  => 'Dibuat Pada',
            'updated-at'  => 'Diperbarui Pada',
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
                    'title' => 'Produk dihapus paksa',
                    'body'  => 'Produk berhasil dihapus secara paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
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
                    'title' => 'Produk dihapus permanen',
                    'body'  => 'Produk berhasil dihapus paksa.',
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

                'entries' => [],

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Pelacakan',

                        'entries' => [
                            'track-inventory' => 'Lacak Persediaan',
                            'track-by'        => 'Lacak Berdasarkan',
                            'expiration-date' => 'Tanggal Kedaluwarsa',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operasi',

                        'entries' => [
                            'routes' => 'Rute',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logistik',

                        'entries' => [
                            'responsible' => 'Penanggung Jawab',
                            'weight'      => 'Berat',
                            'volume'      => 'Volume',
                            'sale-delay'  => 'Waktu Tunggu Pelanggan (Hari)',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Ketertelusuran',

                        'entries' => [
                            'expiration-date'  => 'Tanggal Kedaluwarsa (Hari)',
                            'best-before-date' => 'Tanggal Baik Sebelum (Hari)',
                            'removal-date'     => 'Tanggal Penghapusan (Hari)',
                            'alert-date'       => 'Tanggal Peringatan (Hari)',
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
