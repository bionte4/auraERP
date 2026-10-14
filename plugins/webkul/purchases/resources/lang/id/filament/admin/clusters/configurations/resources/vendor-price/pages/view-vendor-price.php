<?php

return [
    'navigation' => [
        'title' => 'Lihat Daftar Harga Pemasok',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Harga Pemasok dihapus',
                    'body'  => 'Harga pemasok berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Harga Pemasok tidak dapat dihapus',
                    'body'  => 'Harga pemasok tidak dapat dihapus karena sedang digunakan.',
                ],
            ],
        ],
    ],
];
