<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Pengiriman Dihapus',
                    'body'  => 'Pengiriman berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Pengiriman tidak dapat dihapus',
                    'body'  => 'Pengiriman tidak dapat dihapus karena sedang digunakan.',
                ],
            ],
        ],
    ],
];
