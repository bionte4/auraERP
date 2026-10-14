<?php

return [
    'title' => 'Penawaran',

    'navigation' => [
        'title' => 'Penawaran',
    ],

    'global-search' => [
        'customer'  => 'Pelanggan',
        'reference' => 'Referensi',
        'amount'    => 'Jumlah',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Umum',
                'fields' => [
                    'customer'       => 'Pelanggan',
                    'expiration'     => 'Kedaluwarsa',
                    'quotation-date' => 'Tanggal Penawaran',
                    'order-date'     => 'Tanggal Pesanan',
                    'payment-term'   => 'Termin Pembayaran',
                    'price-list'     => 'Daftar Harga',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Baris Pesanan',

                'repeater' => [
                    'products' => [
                        'title'       => 'Produk',
                        'add-product' => 'Tambah Produk',

                        'columns'     => [
                            'product'                    => 'Produk',
                            'product-variants'           => 'Varian Produk',
                            'product-simple'             => 'Produk Sederhana',
                            'quantity'                   => 'Kuantitas',
                            'insufficient-stock-tooltip' => 'Stok tidak mencukupi untuk memenuhi permintaan ini.',
                            'uom'                        => 'UOM',
                            'lead-time'                  => 'Waktu Tunggu',
                            'qty-delivered'              => 'Terkirim',
                            'qty-invoiced'               => 'Difakturkan',
                            'packaging-qty'              => 'Kuantitas Kemasan',
                            'packaging'                  => 'Kemasan',
                            'unit-price'                 => 'Harga Satuan',
                            'cost'                       => 'Biaya',
                            'margin'                     => 'Margin',
                            'taxes'                      => 'Pajak',
                            'amount'                     => 'Jumlah',
                            'margin-percentage'          => 'Margin (%)',
                            'discount-percentage'        => 'Diskon (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Produk',
                            'product-variants'    => 'Varian Produk',
                            'product-simple'      => 'Produk Sederhana',
                            'quantity'            => 'Kuantitas',
                            'uom'                 => 'Satuan Ukur',
                            'lead-time'           => 'Waktu Tunggu',
                            'qty-delivered'       => 'Kuantitas Dikirim',
                            'qty-invoiced'        => 'Kuantitas Difakturkan',
                            'packaging-qty'       => 'Kuantitas Kemasan',
                            'packaging'           => 'Kemasan',
                            'unit-price'          => 'Harga Satuan',
                            'cost'                => 'Biaya',
                            'margin'              => 'Margin',
                            'taxes'               => 'Pajak',
                            'amount'              => 'Jumlah',
                            'margin-percentage'   => 'Margin (%)',
                            'discount-percentage' => 'Diskon (%)',
                        ],

                        'notifications' => [
                            'quantity-below-delivered' => [
                                'title' => 'Tidak Dapat Mengurangi Kuantitas',
                                'body'  => 'Anda tidak dapat mengurangi kuantitas di bawah kuantitas yang dikirim (:qty).',
                            ],
                        ],

                        'delete-action' => [
                            'error' => [
                                'title' => 'Tidak Dapat Menghapus Produk',
                                'body'  => 'Produk tidak dapat dihapus dari pesanan penjualan yang dikonfirmasi.',
                            ],
                        ],

                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Buka produk',
                            ],
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Produk Opsional',
                        'add-product' => 'Tambah Produk',

                        'columns' => [
                            'product'             => 'Produk',
                            'description'         => 'Deskripsi',
                            'quantity'            => 'Kuantitas',
                            'uom'                 => 'Satuan Ukur',
                            'unit-price'          => 'Harga Satuan',
                            'discount-percentage' => 'Diskon (%)',
                        ],

                        'fields'      => [
                            'product'             => 'Produk',
                            'description'         => 'Deskripsi',
                            'quantity'            => 'Kuantitas',
                            'uom'                 => 'Satuan Ukur',
                            'unit-price'          => 'Harga Satuan',
                            'discount-percentage' => 'Diskon (%)',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Tambah Baris Pesanan',
                                    'already-added'  => 'Sudah Ditambahkan ke Pesanan',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Produk ditambahkan',
                                        'body'  => 'Produk berhasil ditambahkan.',
                                    ],

                                    'product-not-found' => [
                                        'title' => 'Produk Tidak Ditemukan',
                                    ],

                                    'product-already-exists' => [
                                        'title' => 'Produk Sudah Ada',
                                        'body'  => 'Produk ini sudah ada pada baris pesanan. Silakan perbarui baris yang sudah ada.',
                                    ],

                                    'missing-product-data' => [
                                        'title' => 'Data Produk Tidak Ada',
                                        'body'  => 'Tidak dapat memproses produk yang dipilih.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Informasi Lainnya',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Penjualan',

                        'fields' => [
                            'sales-person'       => 'Tenaga Penjualan',
                            'customer-reference' => 'Referensi Pelanggan',
                            'tags'               => 'Tag',
                        ],
                    ],

                    'shipping' => [
                        'title'  => 'Pengiriman',
                        'fields' => [
                            'warehouse'       => 'Gudang',
                            'commitment-date' => 'Tanggal Pengiriman',
                        ],
                    ],

                    'tracking' => [
                        'title'  => 'Pelacakan',
                        'fields' => [
                            'source-document' => 'Dokumen Sumber',
                            'medium'          => 'Sedang',
                            'source'          => 'Sumber',
                            'campaign'        => 'Kampanye',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Informasi Tambahan',

                        'fields' => [
                            'company'    => 'Perusahaan',
                            'currency'   => 'Mata Uang',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Syarat & Ketentuan',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'number'             => 'Nomor',
            'status'             => 'Status',
            'delivery-status'    => 'Status Pengiriman',
            'invoice-status'     => 'Status Faktur',
            'creation-date'      => 'Tanggal Pembuatan',
            'commitment-date'    => 'Tanggal Komitmen',
            'expected-date'      => 'Tanggal Perkiraan',
            'customer'           => 'Pelanggan',
            'sales-person'       => 'Tenaga Penjualan',
            'sales-team'         => 'Tim Penjualan',
            'untaxed-amount'     => 'Jumlah Sebelum Pajak',
            'amount-tax'         => 'Jumlah Pajak',
            'amount-total'       => 'Jumlah Total',
            'customer-reference' => 'Referensi Pelanggan',
        ],

        'summarizers' => [
            'total'        => 'Total',
            'taxes'        => 'Pajak',
            'total-amount' => 'Jumlah Total',
        ],

        'filters' => [
            'sales-person'     => 'Tenaga Penjualan',
            'utm-source'       => 'Sumber UTM',
            'company'          => 'Perusahaan',
            'customer'         => 'Pelanggan',
            'journal'          => 'Jurnal',
            'invoice-address'  => 'Alamat Faktur',
            'shipping-address' => 'Alamat Pengiriman',
            'fiscal-position'  => 'Posisi Fiskal',
            'payment-term'     => 'Termin Pembayaran',
            'currency'         => 'Mata Uang',
            'created-at'       => 'Dibuat Pada',
            'updated-at'       => 'Diperbarui Pada',
        ],

        'groups' => [
            'medium'          => 'Sedang',
            'source'          => 'Sumber',
            'team'            => 'Tim',
            'sales-person'    => 'Tenaga Penjualan',
            'currency'        => 'Mata Uang',
            'company'         => 'Perusahaan',
            'customer'        => 'Pelanggan',
            'quotation-date'  => 'Tanggal Penawaran',
            'commitment-date' => 'Tanggal Komitmen',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Penawaran dipulihkan',
                    'body'  => 'Penawaran berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Penawaran dihapus',
                    'body'  => 'Penawaran berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Penawaran dihapus permanen',
                    'body'  => 'Penawaran berhasil dihapus paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Penawaran dipulihkan',
                    'body'  => 'Penawaran berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Penawaran dihapus',
                    'body'  => 'Penawaran berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Penawaran dihapus permanen',
                    'body'  => 'Penawaran berhasil dihapus paksa.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Penawaran dibuat',
                    'body'  => 'Penawaran berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Umum',
                'entries' => [
                    'sale-order'     => 'Pesanan Penjualan',
                    'customer'       => 'Pelanggan',
                    'expiration'     => 'Kedaluwarsa',
                    'quotation-date' => 'Tanggal Penawaran',
                    'payment-term'   => 'Termin Pembayaran',
                ],
            ],
        ],

        'tabs' => [
            'order-line' => [
                'title' => 'Baris Pesanan',

                'repeater' => [
                    'products' => [
                        'title'       => 'Produk',
                        'add-product' => 'Tambah Produk',
                        'entries'     => [
                            'product'             => 'Produk',
                            'product-variants'    => 'Varian Produk',
                            'product-simple'      => 'Produk Sederhana',
                            'quantity'            => 'Kuantitas',
                            'qty-delivered'       => 'Terkirim',
                            'qty-invoiced'        => 'Difakturkan',
                            'uom'                 => 'UOM',
                            'lead-time'           => 'Waktu Tunggu',
                            'packaging-qty'       => 'Kuantitas Kemasan',
                            'packaging'           => 'Kemasan',
                            'unit-price'          => 'Harga Satuan',
                            'cost'                => 'Biaya',
                            'margin'              => 'Margin',
                            'taxes'               => 'Pajak',
                            'amount'              => 'Jumlah',
                            'margin-percentage'   => 'Margin (%)',
                            'discount-percentage' => 'Diskon (%)',
                            'sub-total'           => 'Subtotal',
                        ],
                    ],

                    'product-optional' => [
                        'title'       => 'Produk Opsional',
                        'add-product' => 'Tambah Produk',
                        'entries'     => [
                            'product'             => 'Produk',
                            'description'         => 'Deskripsi',
                            'quantity'            => 'Kuantitas',
                            'uom'                 => 'Satuan Ukur',
                            'unit-price'          => 'Harga Satuan',
                            'discount-percentage' => 'Diskon (%)',
                            'sub-total'           => 'Subtotal',

                            'actions' => [
                                'tooltip' => [
                                    'add-order-line' => 'Tambah Baris Pesanan',
                                ],

                                'notifications' => [
                                    'product-added' => [
                                        'title' => 'Produk ditambahkan',
                                        'body'  => 'Produk berhasil ditambahkan.',
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'other-information' => [
                'title' => 'Informasi Lainnya',

                'fieldset' => [
                    'sales' => [
                        'title' => 'Penjualan',

                        'entries' => [
                            'sales-person'       => 'Tenaga Penjualan',
                            'customer-reference' => 'Referensi Pelanggan',
                            'tags'               => 'Tag',
                        ],
                    ],

                    'shipping' => [
                        'title'   => 'Pengiriman',
                        'entries' => [
                            'commitment-date' => 'Tanggal Pengiriman',
                        ],
                    ],

                    'tracking' => [
                        'title'   => 'Pelacakan',
                        'entries' => [
                            'source-document' => 'Dokumen Sumber',
                            'medium'          => 'Sedang',
                            'source'          => 'Sumber',
                            'campaign'        => 'Kampanye',
                        ],
                    ],

                    'additional-information' => [
                        'title' => 'Informasi Tambahan',

                        'entries' => [
                            'company'  => 'Perusahaan',
                            'currency' => 'Mata Uang',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Syarat & Ketentuan',
            ],
        ],
    ],
];
