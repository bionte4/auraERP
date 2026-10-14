<?php

return [
    'notification' => [
        'title' => 'Produk diperbarui',
        'body'  => 'Produk berhasil diperbarui.',
    ],

    'header-actions' => [
        'update-quantity' => [
            'label'                     => 'Perbarui Kuantitas',
            'modal-heading'             => 'Perbarui Kuantitas Produk',
            'modal-submit-action-label' => 'Perbarui',

            'form' => [
                'fields' => [
                    'on-hand-qty' => 'Kuantitas di Tangan',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Produk dihapus',
                'body'  => 'Produk berhasil dihapus.',
            ],
        ],
    ],
];
