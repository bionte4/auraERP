<?php

return [
    'navigation' => [
        'title' => 'Dropship',
        'group' => 'Transfer',
    ],

    'global-search' => [
        'partner' => 'Mitra',
        'origin'  => 'Asal',
    ],

    'table' => [
        'actions' => [
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

        'bulk-actions' => [
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
    ],
];
