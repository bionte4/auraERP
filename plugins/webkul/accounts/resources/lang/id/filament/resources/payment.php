<?php

return [
    'title' => 'Pembayaran',

    'navigation' => [
        'title' => 'Pembayaran',
        'group' => 'Faktur',
    ],

    'global-search' => [
        'partner' => 'Mitra',
        'amount'  => 'Jumlah',
        'date'    => 'Tanggal',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'payment-type'          => 'Tipe Pembayaran',
                'memo'                  => 'Memo',
                'date'                  => 'Tanggal',
                'amount'                => 'Jumlah',
                'currency'              => 'Mata Uang',
                'payment-method'        => 'Metode Pembayaran',
                'customer'              => 'Pelanggan',
                'vendor'                => 'Pemasok',
                'journal'               => 'Jurnal',
                'customer-bank-account' => 'Rekening Bank Pelanggan',
                'vendor-bank-account'   => 'Rekening Bank Pemasok',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Nama',
            'date'            => 'Tanggal',
            'journal'         => 'Jurnal',
            'payment-method'  => 'Metode Pembayaran',
            'partner'         => 'Mitra',
            'amount-currency' => 'Jumlah (Mata Uang)',
            'amount'          => 'Jumlah',
            'state'           => 'Status',
            'company'         => 'Perusahaan',
            'currency'        => 'Mata Uang',
            'created-by'      => 'Dibuat Oleh',
        ],

        'groups' => [
            'name'                             => 'Nama',
            'company'                          => 'Perusahaan',
            'journal'                          => 'Jurnal',
            'partner'                          => 'Mitra',
            'payment-method-line'              => 'Baris Metode Pembayaran',
            'payment-method'                   => 'Metode Pembayaran',
            'partner-bank-account'             => 'Rekening Bank Mitra',
            'created-at'                       => 'Dibuat Pada',
            'updated-at'                       => 'Diperbarui Pada',
        ],

        'filters' => [
            'company'                          => 'Perusahaan',
            'journal'                          => 'Jurnal',
            'customer-bank-account'            => 'Rekening Bank Pelanggan',
            'payment-method'                   => 'Metode Pembayaran',
            'currency'                         => 'Mata Uang',
            'partner'                          => 'Mitra',
            'payment-method-line'              => 'Baris Metode Pembayaran',
            'created-at'                       => 'Dibuat Pada',
            'updated-at'                       => 'Diperbarui Pada',
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
        'sections' => [
            'payment-information' => [
                'title'   => 'Informasi Pembayaran',
                'entries' => [
                    'state'                 => 'Status',
                    'vendor'                => 'Pemasok',
                    'customer'              => 'Pelanggan',
                    'payment-type'          => 'Tipe Pembayaran',
                    'journal'               => 'Jurnal',
                    'customer-bank-account' => 'Rekening Bank Pelanggan',
                    'vendor-bank-account'   => 'Rekening Bank Pemasok',
                    'amount'                => 'Jumlah',
                    'payment-method'        => 'Metode Pembayaran',
                    'date'                  => 'Tanggal',
                    'memo'                  => 'Memo',
                ],
            ],
        ],
    ],

];
