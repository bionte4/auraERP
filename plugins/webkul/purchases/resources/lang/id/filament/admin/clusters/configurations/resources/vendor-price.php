<?php

return [
    'navigation' => [
        'title' => 'Daftar Harga Pemasok',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',

                'fields' => [
                    'vendor'                      => 'Pemasok',
                    'vendor-product-name'         => 'Nama Produk Pemasok',
                    'vendor-product-name-tooltip' => 'Nama produk pemasok akan muncul pada permintaan penawaran. Biarkan kosong untuk menggunakan nama produk internal.',
                    'vendor-product-code'         => 'Kode Produk Pemasok',
                    'vendor-product-code-tooltip' => 'Kode produk pemasok akan muncul pada permintaan penawaran. Biarkan kosong untuk menggunakan kode internal.',
                    'delay'                       => 'Waktu Tunggu Pengiriman (Hari)',
                    'delay-tooltip'               => 'Waktu tunggu (dalam hari) dari konfirmasi pesanan pembelian hingga penerimaan produk di gudang. Digunakan oleh penjadwal untuk perencanaan pesanan pembelian secara otomatis.',
                ],
            ],

            'prices' => [
                'title'  => 'Harga',

                'fields' => [
                    'product'            => 'Produk',
                    'quantity'           => 'Kuantitas',
                    'quantity-tooltip'   => 'Kuantitas minimum yang harus dibeli dari pemasok ini agar memenuhi harga yang ditentukan. Ini dinyatakan dalam Satuan Ukur Produk pemasok atau, jika tidak diatur, satuan ukur bawaan produk.',
                    'unit-price'         => 'Harga Satuan',
                    'unit-price-tooltip' => 'Harga per unit untuk produk ini dari pemasok, dinyatakan dalam Satuan Ukur Produk pemasok atau, jika tidak diatur, satuan ukur bawaan produk.',
                    'currency'           => 'Mata Uang',
                    'valid-from'         => 'Berlaku Dari',
                    'valid-to'           => 'Berlaku Hingga',
                    'discount'           => 'Diskon (%)',
                    'company'            => 'Perusahaan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'vendor'              => 'Pemasok',
            'vendor-product-name' => 'Nama Produk Pemasok',
            'vendor-product-code' => 'Kode Produk Pemasok',
            'delay'               => 'Waktu Tunggu Pengiriman (Hari)',
            'product'             => 'Produk',
            'quantity'            => 'Kuantitas',
            'unit-price'          => 'Harga Satuan',
            'currency'            => 'Mata Uang',
            'valid-from'          => 'Berlaku Dari',
            'valid-to'            => 'Berlaku Hingga',
            'discount'            => 'Diskon (%)',
            'company'             => 'Perusahaan',
            'created-at'          => 'Dibuat Pada',
            'updated-at'          => 'Diperbarui Pada',
        ],

        'filters' => [
            'vendor'        => 'Filter berdasarkan Pemasok',
            'product'       => 'Filter berdasarkan Produk',
            'currency'      => 'Filter berdasarkan Mata Uang',
            'company'       => 'Filter berdasarkan Perusahaan',
            'price-from'    => 'Harga Minimum',
            'price-to'      => 'Harga Maksimum',
            'min-qty-from'  => 'Kuantitas Minimum Dari',
            'min-qty-to'    => 'Kuantitas Minimum Hingga',
            'starts-from'   => 'Berlaku Mulai Tanggal',
            'ends-before'   => 'Berlaku Hingga Tanggal',
            'created-from'  => 'Dibuat Dari',
            'created-until' => 'Dibuat Hingga',
        ],

        'groups' => [
            'vendor'     => 'Pemasok',
            'product'    => 'Produk',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Harga Pemasok dihapus',
                        'body'  => 'Harga pemasok berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Harga Pemasok tidak dapat dihapus',
                        'body'  => 'Harga pemasok tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Harga Pemasok dihapus',
                        'body'  => 'Harga pemasok berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Harga Pemasok tidak dapat dihapus',
                        'body'  => 'Harga pemasok tidak dapat dihapus karena sedang digunakan.',
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
                    'vendor'                      => 'Pemasok',
                    'vendor-product-name'         => 'Nama Produk Pemasok',
                    'vendor-product-name-tooltip' => 'Nama produk pemasok akan muncul pada permintaan penawaran. Biarkan kosong untuk menggunakan nama produk internal.',
                    'vendor-product-code'         => 'Kode Produk Pemasok',
                    'vendor-product-code-tooltip' => 'Kode produk pemasok akan muncul pada permintaan penawaran. Biarkan kosong untuk menggunakan kode internal.',
                    'delay'                       => 'Waktu Tunggu Pengiriman (Hari)',
                    'delay-tooltip'               => 'Waktu tunggu (dalam hari) dari konfirmasi pesanan pembelian hingga penerimaan produk di gudang. Digunakan oleh penjadwal untuk perencanaan pesanan pembelian secara otomatis.',
                ],
            ],

            'record-information' => [
                'title'  => 'Informasi Rekaman',

                'entries' => [
                    'created-by'   => 'Dibuat Oleh',
                    'created-at'   => 'Dibuat Pada',
                    'last-updated' => 'Terakhir Diperbarui',
                ],
            ],

            'prices' => [
                'title'  => 'Harga',

                'entries' => [
                    'product'            => 'Produk',
                    'quantity'           => 'Kuantitas',
                    'quantity-tooltip'   => 'Kuantitas minimum yang harus dibeli dari pemasok ini agar memenuhi harga yang ditentukan. Ini dinyatakan dalam Satuan Ukur Produk pemasok atau, jika tidak diatur, satuan ukur bawaan produk.',
                    'unit-price'         => 'Harga Satuan',
                    'unit-price-tooltip' => 'Harga per unit untuk produk ini dari pemasok, dinyatakan dalam Satuan Ukur Produk pemasok atau, jika tidak diatur, satuan ukur bawaan produk.',
                    'currency'           => 'Mata Uang',
                    'valid-from'         => 'Berlaku Dari',
                    'valid-to'           => 'Berlaku Hingga',
                    'discount'           => 'Diskon (%)',
                    'company'            => 'Perusahaan',
                ],
            ],
        ],
    ],
];
