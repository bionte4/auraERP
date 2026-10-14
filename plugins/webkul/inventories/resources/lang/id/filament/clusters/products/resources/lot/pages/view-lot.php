<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Lot Dihapus',
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
