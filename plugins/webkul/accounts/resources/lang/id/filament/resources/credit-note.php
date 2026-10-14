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
                    'customer-invoice' => 'Nota Kredit Pelanggan',
                    'customer'         => 'Pelanggan',
                    'invoice-date'     => 'Tanggal Faktur',
                    'due-date'         => 'Tanggal Jatuh Tempo',
                    'payment-term'     => 'Termin Pembayaran',
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
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lokasi Incoterm',
                            'payment-method'    => 'Metode Pembayaran',
                            'auto-post'         => 'Posting Otomatis',
                            'checked'           => 'Dicentang',
                        ],
                    ],

                    'additional-information' => [
                        'title'  => 'Informasi Tambahan',
                        'fields' => [
                            'company'  => 'Perusahaan',
                            'currency' => 'Mata Uang',
                        ],
                    ],

                    'marketing' => [
                        'title'  => 'Pemasaran',
                        'fields' => [
                            'campaign' => 'Kampanye',
                            'medium'   => 'Sedang',
                            'source'   => 'Sumber',
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
                    'customer-invoice' => 'Nota Kredit Pelanggan',
                    'customer'         => 'Pelanggan',
                    'invoice-date'     => 'Tanggal Faktur',
                    'due-date'         => 'Tanggal Jatuh Tempo',
                    'payment-term'     => 'Termin Pembayaran',
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

                        'fieldset' => [
                            'incoterm'          => 'Incoterm',
                            'incoterm-location' => 'Lokasi Incoterm',
                            'payment-method'    => 'Metode Pembayaran',
                            'auto-post'         => 'Posting Otomatis',
                            'checked'           => 'Dicentang',
                        ],
                    ],

                    'additional-information' => [
                        'title'   => 'Informasi Tambahan',
                        'entries' => [
                            'company'  => 'Perusahaan',
                            'currency' => 'Mata Uang',
                        ],
                    ],

                    'marketing' => [
                        'title'   => 'Pemasaran',
                        'entries' => [
                            'campaign' => 'Kampanye',
                            'medium'   => 'Sedang',
                            'source'   => 'Sumber',
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
