<?php

return [
    'notification' => [
        'title' => 'Pajak diperbarui',
        'body'  => 'Pajak berhasil diperbarui.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Pajak dihapus',
                    'body'  => 'Pajak berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Pajak tidak dapat dihapus',
                    'body'  => 'Pajak tidak dapat dihapus karena sedang digunakan.',
                ],

                'invalid-repartition-lines' => [
                    'title' => 'Baris Pembagian Tidak Valid',
                ],
            ],
        ],
    ],
];
