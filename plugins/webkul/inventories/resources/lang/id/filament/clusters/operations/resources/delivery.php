<?php

return [
    'navigation' => [
        'title' => 'Pengiriman',
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

        'bulk-actions' => [
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
    ],
];
