<?php

return [
    'notification' => [
        'title' => 'Afkir diperbarui',
        'body'  => 'Afkir berhasil diperbarui.',
    ],

    'header-actions' => [
        'validate' => [
            'label' => 'Validasi',

            'notification' => [
                'warning' => [
                    'title' => 'Stok tidak mencukupi',
                    'body'  => 'Afkir tidak memiliki stok yang cukup untuk divalidasi.',
                ],

                'success' => [
                    'title' => 'Barang afkir ditandai selesai',
                    'body'  => 'Afkir berhasil ditandai selesai.',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Afkir dihapus',
                    'body'  => 'Afkir berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Afkir tidak dapat dihapus',
                    'body'  => 'Afkir tidak dapat dihapus karena sedang digunakan.',
                ],
            ],
        ],
    ],
];
