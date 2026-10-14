<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Pesanan Dihapus',
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
