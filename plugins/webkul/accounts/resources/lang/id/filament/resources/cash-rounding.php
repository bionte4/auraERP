<?php

return [
    'form' => [
        'fields' => [
            'name'               => 'Nama',
            'rounding-precision' => 'Presisi Pembulatan',
            'rounding-strategy'  => 'Strategi Pembulatan',
            'profit-account'     => 'Akun Laba',
            'loss-account'       => 'Akun Kerugian',
            'rounding-method'    => 'Metode Pembulatan',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Nama',
            'rounding-strategy'    => 'Strategi Pembulatan',
            'rounding-method'      => 'Metode Pembulatan',
            'created-by'           => 'Dibuat Oleh',
            'profit-account'       => 'Akun Laba',
            'loss-account'         => 'Akun Kerugian',
        ],

        'groups' => [
            'name'              => 'Nama',
            'rounding-strategy' => 'Strategi Pembulatan',
            'rounding-method'   => 'Metode Pembulatan',
            'created-by'        => 'Dibuat Oleh',
            'profit-account'    => 'Akun Laba',
            'loss-account'      => 'Akun Kerugian',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pembulatan Kas dihapus',
                    'body'  => 'Pembulatan kas berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pembulatan Kas dihapus',
                    'body'  => 'Pembulatan kas berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'               => 'Nama',
            'rounding-precision' => 'Presisi Pembulatan',
            'rounding-strategy'  => 'Strategi Pembulatan',
            'profit-account'     => 'Akun Laba',
            'loss-account'       => 'Akun Kerugian',
            'rounding-method'    => 'Metode Pembulatan',
        ],
    ],
];
