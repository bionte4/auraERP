<?php

return [
    'notification' => [
        'title' => 'Pengiriman diperbarui',
        'body'  => 'Pengiriman berhasil diperbarui.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Pengiriman dihapus',
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
