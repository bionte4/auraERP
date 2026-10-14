<?php

return [
    'global-search' => [
        'zip-from' => 'Kode Pos Dari',
        'zip-to'   => 'Kode Pos Ke',
        'name'     => 'Nama',
    ],

    'form' => [
        'fields' => [
            'name'                   => 'Nama',
            'foreign-vat'            => 'PPN Asing',
            'country'                => 'Negara',
            'country-group'          => 'Grup Negara',
            'zip-from'               => 'Kode Pos Dari',
            'zip-to'                 => 'Kode Pos Ke',
            'detect-automatically'   => 'Deteksi Secara Otomatis',
            'notes'                  => 'Catatan',
            'company'                => 'Perusahaan',
        ],
        'tabs' => [
            'account-mapping' => [
                'table' => [
                    'columns' => [
                        'source-account'      => 'Akun Sumber',
                        'destination-account' => 'Akun Tujuan',
                    ],
                ],

            ],
            'tax-mapping' => [
                'table' => [
                    'columns' => [
                        'tax-source'      => 'Sumber Pajak',
                        'tax-destination' => 'Tujuan Pajak',
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Nama',
            'company'              => 'Perusahaan',
            'country'              => 'Negara',
            'country-group'        => 'Grup Negara',
            'created-by'           => 'Dibuat Oleh',
            'zip-from'             => 'Kode Pos Dari',
            'zip-to'               => 'Kode Pos Ke',
            'status'               => 'Status',
            'detect-automatically' => 'Deteksi Secara Otomatis',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Posisi Fiskal dihapus',
                    'body'  => 'Posisi fiskal berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Posisi Fiskal dihapus',
                    'body'  => 'Posisi fiskal berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                 => 'Nama',
            'foreign-vat'          => 'PPN Asing',
            'country'              => 'Negara',
            'country-group'        => 'Grup Negara',
            'zip-from'             => 'Kode Pos Dari',
            'zip-to'               => 'Kode Pos Ke',
            'detect-automatically' => 'Deteksi Secara Otomatis',
            'notes'                => 'Catatan',
        ],
    ],
];
