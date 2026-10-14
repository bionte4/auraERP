<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Dropship Dihapus',
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
