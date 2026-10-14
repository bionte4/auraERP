<?php

return [
    'form' => [
        'value'                  => 'Nilai',
        'due'                    => 'Jatuh Tempo',
        'delay-due'              => 'Penundaan Jatuh Tempo',
        'delay-type'             => 'Jenis Penundaan',
        'days-on-the-next-month' => 'Hari pada bulan berikutnya',
        'days'                   => 'Hari',
        'payment-term'           => 'Termin Pembayaran',
    ],

    'table' => [
        'columns' => [
            'due'          => 'Jatuh Tempo',
            'value'        => 'Nilai',
            'value-amount' => 'Jumlah Nilai',
            'after'        => 'Setelah',
            'delay-type'   => 'Jenis Penundaan',
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
];
