<?php

return [
    'notification' => [
        'title' => 'Penerimaan diperbarui',
        'body'  => 'Penerimaan berhasil diperbarui.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Cetak',
        ],

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
];
