<?php

return [
    'notification' => [
        'title' => 'Paket diperbarui',
        'body'  => 'Paket berhasil diperbarui.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Cetak',

            'actions' => [
                'without-content' => [
                    'label' => 'Cetak Barcode',
                ],

                'with-content' => [
                    'label' => 'Cetak Kode Batang Beserta Konten',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Paket dihapus',
                    'body'  => 'Paket berhasil dihapus.',
                ],

                'error' => [
                    'title' => 'Paket tidak dapat dihapus',
                    'body'  => 'Paket tidak dapat dihapus karena sedang digunakan.',
                ],
            ],
        ],
    ],
];
