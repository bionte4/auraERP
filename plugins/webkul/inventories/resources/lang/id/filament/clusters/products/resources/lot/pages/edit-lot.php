<?php

return [
    'notification' => [
        'title' => 'Lot diperbarui',
        'body'  => 'Lot berhasil diperbarui.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Lot dihapus',
                    'body'  => 'Lot berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Lot tidak dapat dihapus',
                    'body'  => 'Lot tidak dapat dihapus karena sedang digunakan.',
                ],
            ],
        ],
    ],
];
