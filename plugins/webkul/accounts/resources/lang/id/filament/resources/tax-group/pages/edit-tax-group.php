<?php

return [
    'notification' => [
        'title' => 'Grup pajak diperbarui',
        'body'  => 'Grup pajak berhasil diperbarui.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Grup pajak dihapus',
                    'body'  => 'Grup pajak berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Grup Pajak tidak dapat dihapus',
                    'body'  => 'Grup pajak tidak dapat dihapus karena sedang digunakan.',
                ],
            ],
        ],
    ],
];
