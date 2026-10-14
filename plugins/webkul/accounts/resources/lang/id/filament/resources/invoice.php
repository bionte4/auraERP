<?php

return [
    'title' => 'Faktur',

    'navigation' => [
        'title' => 'Faktur',
        'group' => 'Faktur',
    ],

    'global-search' => [
        'customer' => 'Pelanggan',
        'date'     => 'Tanggal',
        'due-date' => 'Tanggal Jatuh Tempo',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Umum',

                'fields' => [
                    'customer-invoice' => 'Faktur Pelanggan',
                    'customer'         => 'Pelanggan',
                    'invoice-date'     => 'Tanggal Faktur',
                    'due-date'         => 'Tanggal Jatuh Tempo',
                    'payment-term'     => 'Termin Pembayaran',
                    'journal'          => 'Jurnal',
                    'currency'         => 'Mata Uang',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Baris Faktur',

                'repeater' => [
                    'products' => [
                        'title'       => 'Produk',
                        'add-product' => 'Tambah Produk',

                        'columns' => [
                            'product'             => 'Produk',
                            'quantity'            => 'Kuantitas',
                            'unit'                => 'Satuan',
                            'taxes'               => 'Pajak',
                            'discount-percentage' => 'Diskon (%)',
                            'unit-price'          => 'Harga Satuan',
                            'sub-total'           => 'Subtotal',
                        ],

                        'fields' => [
                            'product'             => 'Produk',
                            'quantity'            => 'Kuantitas',
                            'unit'                => 'Satuan',
                            'taxes'               => 'Pajak',
                            'discount-percentage' => 'Diskon (%)',
                            'unit-price'          => 'Harga Satuan',
                            'sub-total'           => 'Subtotal',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Informasi Lainnya',

                'fieldset' => [
                    'invoice' => [
                        'title'  => 'Faktur',

                        'fields' => [
                            'customer-reference' => 'Referensi Pelanggan',
                            'sales-person'       => 'Tenaga Penjualan',
                            'payment-reference'  => 'Referensi Pembayaran',
                            'recipient-bank'     => 'Bank Penerima',
                            'delivery-date'      => 'Tanggal Pengiriman',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Akuntansi',

                        'fields' => [
                            'company'                 => 'Perusahaan',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Lokasi Incoterm',
                            'fiscal-position'         => 'Posisi Fiskal',
                            'fiscal-position-tooltip' => 'Posisi fiskal digunakan untuk menyesuaikan pajak dan akun berdasarkan lokasi pelanggan.',
                            'cash-rounding'           => 'Metode Pembulatan Kas',
                            'cash-rounding-tooltip'   => 'Menentukan satuan terkecil mata uang yang dapat dibayarkan secara tunai.',
                            'payment-method'          => 'Metode Pembayaran',
                            'auto-post'               => 'Posting Otomatis',
                            'checked'                 => 'Dicentang',
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
        'total'   => 'Total',
        'columns' => [
            'number'           => 'Nomor',
            'state'            => 'Status',
            'created-by'       => 'Dibuat Oleh',
            'customer'         => 'Pelanggan',
            'invoice-date'     => 'Tanggal Faktur',
            'checked'          => 'Dicentang',
            'accounting-date'  => 'Akuntansi',
            'due-date'         => 'Tanggal Jatuh Tempo',
            'source-document'  => 'Dokumen Sumber',
            'reference'        => 'Referensi',
            'sales-person'     => 'Tenaga Penjualan',
            'tax-excluded'     => 'Tidak Termasuk Pajak',
            'tax'              => 'Pajak',
            'total'            => 'Total',
            'amount-due'       => 'Jumlah Terutang',
            'invoice-currency' => 'Mata Uang Faktur',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'name'                         => 'Nama',
            'invoice-partner-display-name' => 'Nama Tampilan Mitra Faktur',
            'invoice-date'                 => 'Tanggal Faktur',
            'checked'                      => 'Dicentang',
            'date'                         => 'Tanggal',
            'invoice-due-date'             => 'Tanggal Jatuh Tempo Faktur',
            'invoice-origin'               => 'Asal Faktur',
            'sales-person'                 => 'Tenaga Penjualan',
            'currency'                     => 'Mata Uang',
            'created-at'                   => 'Dibuat Pada',
            'updated-at'                   => 'Diperbarui Pada',
        ],

        'filters' => [
            'number'                       => 'Nomor',
            'invoice-partner-display-name' => 'Nama Tampilan Mitra Faktur',
            'invoice-date'                 => 'Tanggal Faktur',
            'invoice-due-date'             => 'Tanggal Jatuh Tempo Faktur',
            'invoice-origin'               => 'Asal Faktur',
            'reference'                    => 'Referensi',
            'payment-reference'            => 'Referensi Pembayaran',
            'narration'                    => 'Narasi',
            'partner'                      => 'Mitra',
            'journal'                      => 'Jurnal',
            'fiscal-position'              => 'Posisi Fiskal',
            'currency'                     => 'Mata Uang',
            'company'                      => 'Perusahaan',
            'date'                         => 'Tanggal Akuntansi',
            'delivery-date'                => 'Tanggal Pengiriman',
            'amount-untaxed'               => 'Jumlah Sebelum Pajak',
            'amount-tax'                   => 'Jumlah Pajak',
            'amount-total'                 => 'Jumlah Total',
            'amount-residual'              => 'Jumlah Terutang',
            'checked'                      => 'Dicentang',
            'posted-before'                => 'Diposting Sebelum',
            'is-move-sent'                 => 'Terkirim',
            'created-at'                   => 'Dibuat Pada',
            'updated-at'                   => 'Diperbarui Pada',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pembayaran dihapus',
                    'body'  => 'Pembayaran berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pembayaran dihapus',
                    'body'  => 'Pembayaran berhasil dihapus.',
                ],
            ],
        ],

        'toolbar-actions' => [
            'export' => [
                'label' => 'Ekspor',
            ],
        ],
    ],

    'infolist' => [
        'section' => [
            'general' => [
                'title'   => 'Umum',
                'entries' => [
                    'customer-invoice' => 'Faktur Pelanggan',
                    'customer'         => 'Pelanggan',
                    'invoice-date'     => 'Tanggal Faktur',
                    'due-date'         => 'Tanggal Jatuh Tempo',
                    'payment-term'     => 'Termin Pembayaran',
                    'journal'          => 'Jurnal',
                    'currency'         => 'Mata Uang',
                ],
            ],
        ],

        'tabs' => [
            'invoice-lines' => [
                'title' => 'Baris Faktur',

                'repeater' => [
                    'products' => [
                        'entries' => [
                            'product'             => 'Produk',
                            'quantity'            => 'Kuantitas',
                            'unit'                => 'Satuan Ukur',
                            'taxes'               => 'Pajak',
                            'discount-percentage' => 'Diskon (%)',
                            'unit-price'          => 'Harga Satuan',
                            'sub-total'           => 'Subtotal',
                            'total'               => 'Total',
                        ],
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Informasi Lainnya',

                'fieldset' => [
                    'invoice' => [
                        'title'   => 'Faktur',

                        'entries' => [
                            'customer-reference' => 'Referensi Pelanggan',
                            'sales-person'       => 'Tenaga Penjualan',
                            'payment-reference'  => 'Referensi Pembayaran',
                            'recipient-bank'     => 'Bank Penerima',
                            'delivery-date'      => 'Tanggal Pengiriman',
                        ],
                    ],

                    'accounting' => [
                        'title' => 'Akuntansi',

                        'entries' => [
                            'company'           => 'Perusahaan',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lokasi Incoterm',
                            'payment-method'    => 'Metode Pembayaran',
                            'cash-rounding'     => 'Metode Pembulatan Kas',
                            'fiscal-position'   => 'Posisi Fiskal',
                            'auto-post'         => 'Posting Otomatis',
                            'checked'           => 'Dicentang',
                        ],
                    ],
                ],
            ],

            'term-and-conditions' => [
                'title' => 'Syarat & Ketentuan',
            ],

            'journal-items' => [
                'title' => 'Item Jurnal',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Akun',
                        'partner'  => 'Mitra',
                        'label'    => 'Label',
                        'currency' => 'Mata Uang',
                        'due-date' => 'Tanggal Jatuh Tempo',
                        'taxes'    => 'Pajak',
                        'debit'    => 'Debit',
                        'credit'   => 'Kredit',
                    ],
                ],
            ],
        ],
    ],

    'summary' => [
        'actions' => [
            'reconcile' => [
                'label' => 'Tambah',
            ],

            'unreconcile' => [
                'label' => 'Putuskan Tautan',
            ],
        ],
    ],

];
