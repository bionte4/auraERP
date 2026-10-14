<?php

return [
    'table' => [
        'columns' => [
            'on-hand'    => 'Di Tangan',
            'forecasted' => 'Diperkirakan',
        ],
    ],

    'navigation' => [
        'title' => 'Produk',
        'group' => 'Persediaan',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Persediaan',

                'fieldsets' => [
                    'tracking' => [
                        'title' => 'Pelacakan',

                        'fields' => [
                            'track-inventory'              => 'Lacak Persediaan',
                            'track-inventory-hint-tooltip' => 'Produk yang dapat disimpan adalah produk yang memerlukan pengelolaan persediaan..',
                            'track-by'                     => 'Lacak Berdasarkan',
                            'expiration-date'              => 'Tanggal Kedaluwarsa',
                            'expiration-date-hint-tooltip' => 'Jika dipilih, Anda dapat menentukan tanggal kedaluwarsa untuk produk beserta nomor lot/seri terkait.',
                        ],
                    ],

                    'operation' => [
                        'title' => 'Operasi',

                        'fields' => [
                            'routes'              => 'Rute',
                            'routes-hint-tooltip' => 'Berdasarkan modul yang terpasang, pengaturan ini memungkinkan Anda menentukan rute produk, seperti pembelian, manufaktur, atau pengisian ulang sesuai pesanan.',
                        ],
                    ],

                    'logistics' => [
                        'title' => 'Logistik',

                        'fields' => [
                            'responsible'              => 'Penanggung Jawab',
                            'responsible-hint-tooltip' => 'Waktu tunggu pengiriman (dalam hari) menunjukkan durasi yang dijanjikan antara konfirmasi pesanan penjualan dan pengiriman produk.',
                            'weight'                   => 'Berat',
                            'volume'                   => 'Volume',
                            'sale-delay'               => 'Waktu Tunggu Pelanggan (Hari)',
                            'sale-delay-hint-tooltip'  => 'Waktu tunggu pengiriman (dalam hari) menunjukkan durasi yang dijanjikan antara konfirmasi pesanan penjualan dan pengiriman produk.',
                        ],
                    ],

                    'traceability' => [
                        'title' => 'Ketertelusuran',

                        'fields' => [
                            'expiration-date'               => 'Tanggal Kedaluwarsa (Hari)',
                            'expiration-date-hint-tooltip'  => 'Jika dipilih, Anda dapat mengatur tanggal kedaluwarsa untuk produk beserta nomor lot/seri terkait.',
                            'best-before-date'              => 'Tanggal Baik Sebelum (Hari)',
                            'best-before-date-hint-tooltip' => 'Jumlah hari sebelum tanggal kedaluwarsa saat produk mulai menurun kualitasnya, meskipun masih aman digunakan. Ini dihitung berdasarkan nomor lot/seri.',
                            'removal-date'                  => 'Tanggal Penghapusan (Hari)',
                            'removal-date-hint-tooltip'     => 'Jumlah hari sebelum tanggal kedaluwarsa saat produk harus dikeluarkan dari stok. Ini dihitung berdasarkan nomor lot/seri.',
                            'alert-date'                    => 'Tanggal Peringatan (Hari)',
                            'alert-date-hint-tooltip'       => 'Jumlah hari sebelum tanggal kedaluwarsa saat peringatan harus dipicu untuk nomor lot/seri. Ini dihitung berdasarkan nomor lot/seri.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Tambahan',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Persediaan',

                'entries' => [
                ],

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
        ],
    ],
];
