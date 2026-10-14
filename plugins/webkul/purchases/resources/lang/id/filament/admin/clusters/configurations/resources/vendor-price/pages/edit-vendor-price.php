<?php

return [
    'navigation' => [
        'title' => 'Ubah Daftar Harga Pemasok',
    ],

    'notification' => [
        'title' => 'Harga Pemasok diperbarui',
        'body'  => 'Harga pemasok berhasil diperbarui.',
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
