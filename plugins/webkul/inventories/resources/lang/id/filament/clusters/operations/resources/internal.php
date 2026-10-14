<?php

return [
    'navigation' => [
        'title' => 'Transfer Internal',
        'group' => 'Transfer',
    ],

    'global-search' => [
        'origin' => 'Asal',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Transfer Internal dihapus',
                        'body'  => 'Transfer internal berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Transfer internal tidak dapat dihapus',
                        'body'  => 'Transfer internal tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Transfer Internal dihapus',
                        'body'  => 'Transfer internal berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Transfer internal tidak dapat dihapus',
                        'body'  => 'Transfer internal tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],
];
