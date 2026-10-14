<?php

return [
    'notification' => [
        'title' => 'Pesanan diperbarui',
        'body'  => 'Pesanan berhasil diperbarui.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Konfirmasi',
        ],

        'close' => [
            'label' => 'Tutup',
        ],

        'cancel' => [
            'label' => 'Batal',
        ],

        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Pesanan dihapus',
                    'body'  => 'Pesanan berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Pesanan tidak dapat dihapus',
                    'body'  => 'Pesanan tidak dapat dihapus karena sedang digunakan.',
                ],
            ],
        ],
    ],
];
