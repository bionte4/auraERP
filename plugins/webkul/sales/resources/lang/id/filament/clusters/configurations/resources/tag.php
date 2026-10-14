<?php

return [
    'title' => 'Tag',

    'navigation' => [
        'title' => 'Tag',
        'group' => 'Pesanan Penjualan',
    ],

    'form' => [
        'fields' => [
            'name'  => 'Nama',
            'color' => 'Warna',
        ],
    ],

    'table' => [
        'columns' => [
            'created-by' => 'Dibuat oleh',
            'name'       => 'Nama',
            'color'      => 'Warna',
        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tag Produk diperbarui',
                    'body'  => 'Tag produk berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag Produk dihapus',
                    'body'  => 'Tag produk berhasil dihapus.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tag Produk dihapus',
                    'body'  => 'Tag produk berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'  => 'Nama',
            'color' => 'Warna',
        ],
    ],
];
