<?php

return [
    'title' => 'Faktur',

    'navigation' => [
        'title' => 'Faktur',
        'group' => 'Faktur',
    ],

    'global-search' => [
        'vendor'   => 'Pemasok',
        'date'     => 'Tanggal',
        'due-date' => 'Tanggal Jatuh Tempo',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Umum',
                'fields' => [
                    'vendor-bill'       => 'Tagihan Pemasok',
                    'vendor'            => 'Pemasok',
                    'project'           => 'Proyek',
                    'project-helper'    => 'Opsional — kaitkan bill ini ke proyek untuk kontrol anggaran.',
                    'bill-date'         => 'Tanggal Tagihan',
                    'bill-reference'    => 'Referensi Tagihan',
                    'accounting-date'   => 'Tanggal Akuntansi',
                    'payment-reference' => 'Referensi Pembayaran',
                    'recipient-bank'    => 'Bank Penerima',
                    'due-date'          => 'Tanggal Jatuh Tempo',
                    'payment-term'      => 'Termin Pembayaran',
                    'journal'           => 'Jurnal',
                    'currency'          => 'Mata Uang',
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
                    'accounting' => [
                        'title' => 'Akuntansi',

                        'fields' => [
                            'company'                 => 'Perusahaan',
                            'incoterm'                => 'Incoterm',
                            'incoterm-location'       => 'Lokasi Incoterm',
                            'payment-method'          => 'Metode Pembayaran',
                            'fiscal-position'         => 'Posisi Fiskal',
                            'fiscal-position-tooltip' => 'Posisi fiskal digunakan untuk menyesuaikan pajak dan akun berdasarkan lokasi pelanggan.',
                            'cash-rounding'           => 'Metode Pembulatan Kas',
                            'cash-rounding-tooltip'   => 'Menentukan satuan terkecil mata uang yang dapat dibayarkan secara tunai.',
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
            'customer'         => 'Pelanggan',
            'bill-date'        => 'Tanggal Tagihan',
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
            'bill-currency'    => 'Mata Uang Tagihan',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'name'                         => 'Nama',
            'bill-partner-display-name'    => 'Nama Tampilan Mitra Tagihan',
            'bill-date'                    => 'Tanggal Tagihan',
            'checked'                      => 'Dicentang',
            'date'                         => 'Tanggal',
            'bill-due-date'                => 'Tanggal Jatuh Tempo Tagihan',
            'bill-origin'                  => 'Asal Tagihan',
            'sales-person'                 => 'Tenaga Penjualan',
            'currency'                     => 'Mata Uang',
            'created-at'                   => 'Dibuat Pada',
            'updated-at'                   => 'Diperbarui Pada',
        ],

        'filters' => [
            'number'                    => 'Nomor',
            'bill-partner-display-name' => 'Nama Tampilan Mitra Tagihan',
            'bill-date'                 => 'Tanggal Tagihan',
            'bill-due-date'             => 'Tanggal Jatuh Tempo Tagihan',
            'bill-origin'               => 'Asal Tagihan',
            'reference'                 => 'Referensi',
            'payment-reference'         => 'Referensi Pembayaran',
            'narration'                 => 'Narasi',
            'partner'                   => 'Mitra',
            'journal'                   => 'Jurnal',
            'fiscal-position'           => 'Posisi Fiskal',
            'currency'                  => 'Mata Uang',
            'company'                   => 'Perusahaan',
            'date'                      => 'Tanggal Akuntansi',
            'delivery-date'             => 'Tanggal Pengiriman',
            'amount-untaxed'            => 'Jumlah Sebelum Pajak',
            'amount-tax'                => 'Jumlah Pajak',
            'amount-total'              => 'Jumlah Total',
            'amount-residual'           => 'Jumlah Terutang',
            'checked'                   => 'Dicentang',
            'posted-before'             => 'Diposting Sebelum',
            'is-move-sent'              => 'Terkirim',
            'created-at'                => 'Dibuat Pada',
            'updated-at'                => 'Diperbarui Pada',
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
                    'vendor-invoice'    => 'Faktur Pemasok',
                    'vendor'            => 'Pemasok',
                    'bill-date'         => 'Tanggal Tagihan',
                    'bill-reference'    => 'Referensi Tagihan',
                    'accounting-date'   => 'Tanggal Akuntansi',
                    'payment-reference' => 'Referensi Pembayaran',
                    'recipient-bank'    => 'Bank Penerima',
                    'due-date'          => 'Tanggal Jatuh Tempo',
                    'payment-term'      => 'Termin Pembayaran',
                    'journal'           => 'Jurnal',
                    'currency'          => 'Mata Uang',
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

                        'entries' => [
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
                    'accounting' => [
                        'title' => 'Akuntansi',

                        'entries' => [
                            'company'           => 'Perusahaan',
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lokasi Incoterm',
                            'payment-method'    => 'Metode Pembayaran',
                            'checked'           => 'Dicentang',
                            'fiscal-position'   => 'Posisi Fiskal',
                            'cash-rounding'     => 'Metode Pembulatan Kas',
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
                        'due-date' => 'Tanggal Jatuh Tempo',
                        'currency' => 'Mata Uang',
                        'taxes'    => 'Pajak',
                        'debit'    => 'Debit',
                        'credit'   => 'Kredit',
                    ],
                ],
            ],
        ],
    ],
];
