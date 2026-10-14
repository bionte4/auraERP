<?php

return [
    'title' => 'Mitra',

    'navigation' => [
        'title' => 'Mitra',
    ],

    'form' => [
        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Penjualan',

                        'fields' => [
                            'sales-person'   => 'Tenaga Penjualan',
                            'payment-terms'  => 'Termin Pembayaran',
                            'payment-method' => 'Metode Pembayaran',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Pembelian',

                        'fields' => [
                            'payment-terms'  => 'Termin Pembayaran',
                            'payment-method' => 'Metode Pembayaran',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Informasi Fiskal',

                        'fields' => [
                            'fiscal-position'    => 'Posisi Fiskal',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Penagihan',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Faktur Pelanggan',

                        'fields' => [
                            'invoice-sending-method'   => 'Metode Pengiriman Faktur',
                            'invoice-edi-format-store' => 'Format eInvoice',
                            'peppol-eas'               => 'Alamat Peppol',
                            'endpoint'                 => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Entri Akuntansi',

                        'fields' => [
                            'account-receivable' => 'Piutang Usaha',
                            'account-payable'    => 'Utang Usaha',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Otomatisasi',

                        'fields' => [
                            'auto-post-bills'                => 'Posting Tagihan Otomatis',
                            'ignore-abnormal-invoice-amount' => 'Abaikan Jumlah Faktur Tidak Wajar',
                            'ignore-abnormal-invoice-date'   => 'Abaikan Tanggal Faktur Tidak Wajar',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Catatan Internal',
            ],
        ],
    ],

    'infolist' => [

        'tabs' => [
            'sales-purchases' => [
                'fieldsets' => [
                    'sales' => [
                        'title' => 'Penjualan',

                        'entries' => [
                            'sales-person'   => 'Tenaga Penjualan',
                            'payment-terms'  => 'Termin Pembayaran',
                            'payment-method' => 'Metode Pembayaran',
                        ],
                    ],

                    'purchase' => [
                        'title' => 'Pembelian',

                        'entries' => [
                            'payment-terms'  => 'Termin Pembayaran',
                            'payment-method' => 'Metode Pembayaran',
                        ],
                    ],

                    'fiscal-information' => [
                        'title' => 'Informasi Fiskal',

                        'entries' => [
                            'fiscal-position'    => 'Posisi Fiskal',
                        ],
                    ],
                ],
            ],

            'invoicing' => [
                'title'  => 'Penagihan',

                'fieldsets' => [
                    'customer-invoices' => [
                        'title' => 'Faktur Pelanggan',

                        'entries' => [
                            'invoice-sending-method'   => 'Metode Pengiriman Faktur',
                            'invoice-edi-format-store' => 'Format eInvoice',
                            'peppol-eas'               => 'Alamat Peppol',
                            'endpoint'                 => 'Endpoint',
                        ],
                    ],

                    'accounting-entries' => [
                        'title' => 'Entri Akuntansi',

                        'entries' => [
                            'account-receivable' => 'Piutang Usaha',
                            'account-payable'    => 'Utang Usaha',
                        ],
                    ],

                    'automation' => [
                        'title' => 'Otomatisasi',

                        'entries' => [
                            'auto-post-bills'                => 'Posting Tagihan Otomatis',
                            'ignore-abnormal-invoice-amount' => 'Abaikan Jumlah Faktur Tidak Wajar',
                            'ignore-abnormal-invoice-date'   => 'Abaikan Tanggal Faktur Tidak Wajar',
                        ],
                    ],
                ],
            ],

            'internal-notes' => [
                'title' => 'Catatan Internal',
            ],
        ],
    ],
];
