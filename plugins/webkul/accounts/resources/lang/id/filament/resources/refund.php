<?php

return [
    'title' => 'Faktur',

    'navigation' => [
        'title' => 'Faktur',
        'group' => 'Faktur',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Umum',
                'fields' => [
                    'vendor-credit-note' => 'Nota Kredit Pemasok',
                    'vendor'             => 'Pemasok',
                    'bill-date'          => 'Tanggal Tagihan',
                    'bill-reference'     => 'Referensi Tagihan',
                    'accounting-date'    => 'Tanggal Akuntansi',
                    'payment-reference'  => 'Referensi Pembayaran',
                    'recipient-bank'     => 'Bank Penerima',
                    'due-date'           => 'Tanggal Jatuh Tempo',
                    'payment-term'       => 'Termin Pembayaran',
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
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lokasi Incoterm',
                        ],
                    ],

                    'secured' => [
                        'title'  => 'Diamankan',
                        'fields' => [
                            'payment-method' => 'Metode Pembayaran',
                            'auto-post'      => 'Posting Otomatis',
                            'checked'        => 'Dicentang',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Informasi Tambahan',
                        'fields' => [
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
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lokasi Incoterm',
                        ],
                    ],

                    'secured' => [
                        'title'   => 'Diamankan',
                        'entries' => [
                            'payment-method' => 'Metode Pembayaran',
                            'auto-post'      => 'Posting Otomatis',
                            'checked'        => 'Dicentang',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Informasi Tambahan',
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
