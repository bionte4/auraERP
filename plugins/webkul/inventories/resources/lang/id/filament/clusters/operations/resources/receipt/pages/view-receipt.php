<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Penerimaan Dihapus',
                    'body'  => 'Penerimaan berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Penerimaan tidak dapat dihapus',
                    'body'  => 'Penerimaan tidak dapat dihapus karena sedang digunakan.',
                ],
            ],
        ],
    ],
];
