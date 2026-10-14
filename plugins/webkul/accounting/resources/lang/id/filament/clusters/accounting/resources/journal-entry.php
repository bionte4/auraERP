<?php

return [
    'title' => 'Entri Jurnal',

    'navigation' => [
        'title' => 'Entri Jurnal',
    ],

    'record-sub-navigation' => [
        'payment' => 'Pembayaran',
    ],

    'global-search' => [
        'number'   => 'Nomor',
        'partner'  => 'Mitra',
        'date'     => 'Tanggal Faktur',
        'due-date' => 'Tanggal Jatuh Tempo Faktur',
    ],

    'form' => [
        'section' => [
            'general' => [
                'title'  => 'Umum',

                'fields' => [
                    'reference'       => 'Referensi',
                    'accounting-date' => 'Tanggal Akuntansi',
                    'journal'         => 'Jurnal',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Item Jurnal',

                'repeater' => [
                    'title'       => 'Item',
                    'add-item'    => 'Tambah Item',

                    'columns' => [
                        'account'                  => 'Akun',
                        'partner'                  => 'Mitra',
                        'label'                    => 'Label',
                        'amount-currency'          => 'Jumlah (Mata Uang)',
                        'currency'                 => 'Mata Uang',
                        'taxes'                    => 'Pajak',
                        'debit'                    => 'Debit',
                        'credit'                   => 'Kredit',
                        'discount-amount-currency' => 'Jumlah Diskon (Mata Uang)',
                    ],

                    'fields' => [
                        'account'                  => 'Akun',
                        'partner'                  => 'Mitra',
                        'label'                    => 'Label',
                        'amount-currency'          => 'Jumlah (Mata Uang)',
                        'currency'                 => 'Mata Uang',
                        'taxes'                    => 'Pajak',
                        'debit'                    => 'Debit',
                        'credit'                   => 'Kredit',
                        'discount-amount-currency' => 'Jumlah Diskon (Mata Uang)',
                    ],
                ],
            ],

            'other-information' => [
                'title'    => 'Informasi Lainnya',

                'fields' => [
                    'checked'                 => 'Dicentang',
                    'company'                 => 'Perusahaan',
                    'fiscal-position'         => 'Posisi Fiskal',
                    'fiscal-position-tooltip' => 'Posisi fiskal digunakan untuk menyesuaikan pajak dan akun berdasarkan lokasi pelanggan.',
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
            'invoice-date' => 'Tanggal Faktur',
            'date'         => 'Tanggal',
            'number'       => 'Nomor',
            'partner'      => 'Mitra',
            'reference'    => 'Referensi',
            'journal'      => 'Jurnal',
            'company'      => 'Perusahaan',
            'total'        => 'Total',
            'state'        => 'Status',
            'checked'      => 'Dicentang',
        ],

        'summarizers' => [
            'total' => 'Total',
        ],

        'groups' => [
            'partner'        => 'Mitra',
            'journal'        => 'Jurnal',
            'state'          => 'Status',
            'payment-method' => 'Metode Pembayaran',
            'date'           => 'Tanggal',
            'invoice-date'   => 'Tanggal Faktur',
            'company'        => 'Perusahaan',
        ],

        'filters' => [
            'number'                       => 'Nomor',
            'invoice-partner-display-name' => 'Nama Tampilan Mitra Faktur',
            'invoice-date'                 => 'Tanggal Faktur',
            'invoice-due-date'             => 'Tanggal Jatuh Tempo Faktur',
            'invoice-origin'               => 'Asal Faktur',
            'reference'                    => 'Referensi',
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
                    'number'          => 'Nomor',
                    'reference'       => 'Referensi',
                    'accounting-date' => 'Tanggal Akuntansi',
                    'journal'         => 'Jurnal',
                ],
            ],
        ],

        'tabs' => [
            'lines' => [
                'title' => 'Item Jurnal',

                'repeater' => [
                    'entries' => [
                        'account'  => 'Akun',
                        'partner'  => 'Mitra',
                        'label'    => 'Label',
                        'currency' => 'Mata Uang',
                        'taxes'    => 'Pajak',
                        'debit'    => 'Debit',
                        'credit'   => 'Kredit',
                    ],
                ],
            ],

            'other-information' => [
                'title' => 'Informasi Lainnya',

                'fieldset' => [
                    'accounting' => [
                        'title' => 'Akuntansi',

                        'entries' => [
                            'company'         => 'Perusahaan',
                            'fiscal-position' => 'Posisi Fiskal',
                            'checked'         => 'Dicentang',
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
