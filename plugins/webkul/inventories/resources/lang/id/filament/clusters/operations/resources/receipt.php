<?php

return [
    'navigation' => [
        'title' => 'Penerimaan',
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
                        'title' => 'Penerimaan dihapus',
                        'body'  => 'Penerimaan berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Penerimaan tidak dapat dihapus',
                        'body'  => 'Penerimaan tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Penerimaan dihapus',
                        'body'  => 'Penerimaan berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Penerimaan tidak dapat dihapus',
                        'body'  => 'Penerimaan tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],
];
