<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Transfer Internal Dihapus',
                    'body'  => 'Transfer internal berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Transfer internal tidak dapat dihapus',
                    'body'  => 'Transfer internal tidak dapat dihapus karena sedang digunakan.',
                ],
            ],
        ],
    ],
];
