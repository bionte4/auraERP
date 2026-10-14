<?php

return [
    'notification' => [
        'title' => 'Dropship diperbarui',
        'body'  => 'Dropship berhasil diperbarui.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Dropship dihapus',
                    'body'  => 'Dropship berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Dropship tidak dapat dihapus',
                    'body'  => 'Dropship tidak dapat dihapus karena sedang digunakan.',
                ],
            ],
        ],
    ],
];
