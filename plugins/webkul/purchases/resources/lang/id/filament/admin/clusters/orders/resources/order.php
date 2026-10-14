<?php

return [
    'global-search' => [
        'vendor'    => 'Pemasok',
        'reference' => 'Referensi',
        'amount'    => 'Jumlah',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'vendor'                   => 'Pemasok',
                    'vendor-reference'         => 'Referensi Pemasok',
                    'vendor-reference-tooltip' => 'Nomor referensi pesanan penjualan atau penawaran yang diberikan oleh pemasok. Nomor ini digunakan untuk pencocokan saat menerima produk, karena referensi ini biasanya tercantum pada pesanan pengiriman pemasok.',
                    'agreement'                => 'Perjanjian',
                    'currency'                 => 'Mata Uang',
                    'confirmation-date'        => 'Tanggal Konfirmasi',
                    'order-deadline'           => 'Batas Waktu Pesanan',
                    'expected-arrival'         => 'Perkiraan Kedatangan',
                    'confirmed-by-vendor'      => 'Dikonfirmasi oleh Pemasok',
                    'deliver-to'               => 'Kirim Ke',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produk',

                'repeater' => [
                    'products' => [
                        'title'            => 'Produk',
                        'add-product-line' => 'Tambah Produk',

                        'fields' => [
                            'product'             => 'Produk',
                            'expected-arrival'    => 'Perkiraan Kedatangan',
                            'quantity'            => 'Kuantitas',
                            'received'            => 'Diterima',
                            'billed'              => 'Ditagih',
                            'unit'                => 'Satuan',
                            'packaging-qty'       => 'Kuantitas Kemasan',
                            'packaging'           => 'Kemasan',
                            'taxes'               => 'Pajak',
                            'discount-percentage' => 'Diskon (%)',
                            'unit-price'          => 'Harga Satuan',
                            'amount'              => 'Jumlah',
                        ],

                        'notifications' => [
                            'quantity-below-received' => [
                                'title' => 'Tidak Dapat Mengurangi Kuantitas',
                                'body'  => 'Anda tidak dapat mengurangi kuantitas di bawah kuantitas yang diterima (:qty).',
                            ],

                            'blanket-order-qty-limit' => [
                                'title' => 'Kuantitas melebihi batas pesanan blanket',
                                'body'  => 'Kuantitas produk (:product_qty) melebihi kuantitas yang tersedia (:available_qty) dari pesanan menyeluruh.',
                            ],
                        ],

                        'columns' => [
                            'product'             => 'Produk',
                            'expected-arrival'    => 'Perkiraan Kedatangan',
                            'quantity'            => 'Kuantitas',
                            'received'            => 'Diterima',
                            'billed'              => 'Ditagih',
                            'unit'                => 'Satuan',
                            'packaging-qty'       => 'Kuantitas Kemasan',
                            'packaging'           => 'Kemasan',
                            'taxes'               => 'Pajak',
                            'discount-percentage' => 'Diskon (%)',
                            'unit-price'          => 'Harga Satuan',
                            'amount'              => 'Jumlah',
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Tidak Dapat Menghapus Produk',
                                'body'  => 'Produk tidak dapat dihapus dari pesanan pembelian yang dikonfirmasi.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Buka produk',
                            ],
                        ],
                    ],

                    'section' => [
                        'title' => 'Tambah Bagian',

                        'fields' => [],
                    ],

                    'note' => [
                        'title' => 'Tambah Catatan',

                        'fields' => [],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Informasi Tambahan',

                'fields' => [
                    'buyer'             => 'Pembeli',
                    'company'           => 'Perusahaan',
                    'source-document'   => 'Dokumen Sumber',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'Ketentuan Komersial Internasional (Incoterms) adalah serangkaian ketentuan perdagangan baku yang digunakan dalam transaksi global untuk menetapkan tanggung jawab antara pembeli dan penjual.',
                    'incoterm-location' => 'Lokasi Incoterm',
                    'payment-term'      => 'Termin Pembayaran',
                    'fiscal-position'   => 'Posisi Fiskal',
                ],
            ],

            'terms' => [
                'title' => 'Syarat dan Ketentuan',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'         => 'Favorit',
            'priority'         => 'Prioritas',
            'vendor-reference' => 'Referensi Pemasok',
            'reference'        => 'Referensi',
            'vendor'           => 'Pemasok',
            'buyer'            => 'Pembeli',
            'company'          => 'Perusahaan',
            'order-deadline'   => 'Batas Waktu Pesanan',
            'source-document'  => 'Dokumen Sumber',
            'untaxed-amount'   => 'Jumlah Sebelum Pajak',
            'total-amount'     => 'Jumlah Total',
            'status'           => 'Status',
            'billing-status'   => 'Status Penagihan',
            'receipt-status'   => 'Status Penerimaan',
            'currency'         => 'Mata Uang',
        ],

        'groups' => [
            'vendor'     => 'Pemasok',
            'buyer'      => 'Pembeli',
            'state'      => 'Status',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'status'           => 'Status',
            'vendor-reference' => 'Referensi Pemasok',
            'reference'        => 'Referensi',
            'untaxed-amount'   => 'Jumlah Sebelum Pajak',
            'total-amount'     => 'Jumlah Total',
            'order-deadline'   => 'Batas Waktu Pesanan',
            'vendor'           => 'Pemasok',
            'buyer'            => 'Pembeli',
            'company'          => 'Perusahaan',
            'payment-term'     => 'Termin Pembayaran',
            'incoterm'         => 'Incoterm',
            'status'           => 'Status',
            'created-at'       => 'Dibuat Pada',
            'updated-at'       => 'Diperbarui Pada',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pesanan dihapus',
                        'body'  => 'Pesanan berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Pesanan tidak dapat dihapus',
                        'body'  => 'Pesanan tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pesanan dihapus',
                        'body'  => 'Pesanan berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Pesanan tidak dapat dihapus',
                        'body'  => 'Pesanan tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'tax' => 'Pajak',
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'entries' => [
                    'purchase-order'           => 'Pesanan Pembelian',
                    'vendor'                   => 'Pemasok',
                    'vendor-reference'         => 'Referensi Pemasok',
                    'vendor-reference-tooltip' => 'Nomor referensi pesanan penjualan atau penawaran yang diberikan oleh pemasok. Nomor ini digunakan untuk pencocokan saat menerima produk, karena referensi ini biasanya tercantum pada pesanan pengiriman pemasok.',
                    'agreement'                => 'Perjanjian',
                    'currency'                 => 'Mata Uang',
                    'confirmation-date'        => 'Tanggal Konfirmasi',
                    'order-deadline'           => 'Batas Waktu Pesanan',
                    'expected-arrival'         => 'Perkiraan Kedatangan',
                    'confirmed-by-vendor'      => 'Dikonfirmasi oleh Pemasok',
                ],
            ],
        ],

        'tabs' => [
            'products' => [
                'title' => 'Produk',

                'repeater' => [
                    'products' => [
                        'title'            => 'Produk',
                        'add-product-line' => 'Tambah Produk',

                        'entries' => [
                            'product'             => 'Produk',
                            'expected-arrival'    => 'Perkiraan Kedatangan',
                            'quantity'            => 'Kuantitas',
                            'received'            => 'Diterima',
                            'billed'              => 'Ditagih',
                            'unit'                => 'Satuan',
                            'packaging-qty'       => 'Kuantitas Kemasan',
                            'packaging'           => 'Kemasan',
                            'taxes'               => 'Pajak',
                            'discount-percentage' => 'Diskon (%)',
                            'unit-price'          => 'Harga Satuan',
                            'amount'              => 'Jumlah',
                        ],
                    ],

                    'section' => [
                        'title' => 'Tambah Bagian',
                    ],

                    'note' => [
                        'title' => 'Tambah Catatan',
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Informasi Tambahan',

                'entries' => [
                    'buyer'             => 'Pembeli',
                    'company'           => 'Perusahaan',
                    'source-document'   => 'Dokumen Sumber',
                    'incoterm'          => 'Incoterm',
                    'incoterm-tooltip'  => 'Ketentuan Komersial Internasional (Incoterms) adalah serangkaian ketentuan perdagangan baku yang digunakan dalam transaksi global untuk menetapkan tanggung jawab antara pembeli dan penjual.',
                    'incoterm-location' => 'Lokasi Incoterm',
                    'payment-term'      => 'Termin Pembayaran',
                    'fiscal-position'   => 'Posisi Fiskal',
                ],
            ],

            'terms' => [
                'title' => 'Syarat dan Ketentuan',
            ],
        ],
    ],
];
