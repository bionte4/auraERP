<?php

return [
    'form' => [
        'fields' => [
            'tax-source'      => 'Sumber Pajak',
            'tax-destination' => 'Tujuan Pajak',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source'      => 'Sumber Pajak',
            'tax-destination' => 'Tujuan Pajak',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Syarat Jatuh Tempo Pembayaran diperbarui',
                    'body'  => 'Ketentuan jatuh tempo pembayaran berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Syarat Jatuh Tempo Pembayaran dihapus',
                    'body'  => 'Ketentuan jatuh tempo pembayaran berhasil dihapus.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Syarat Jatuh Tempo Pembayaran dibuat',
                    'body'  => 'Ketentuan jatuh tempo pembayaran berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'tax-source'      => 'Sumber Pajak',
            'tax-destination' => 'Tujuan Pajak',
        ],
    ],
];
