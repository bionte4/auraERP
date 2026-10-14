<?php

return [
    'notification' => [
        'title' => 'Transfer Internal diperbarui',
        'body'  => 'Transfer internal berhasil diperbarui.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

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
];
