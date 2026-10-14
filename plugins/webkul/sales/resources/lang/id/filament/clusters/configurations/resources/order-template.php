<?php

return [
    'title' => 'Produk Templat Pesanan',

    'navigation' => [
        'title' => 'Produk Templat Pesanan',
        'group' => 'Pesanan Penjualan',
    ],

    'global-search' => [
        'name'    => 'Nama',
    ],

    'form' => [
        'fields' => [
            'sort'           => 'Urutkan',
            'order-template' => 'Templat Pesanan',
            'company'        => 'Perusahaan',
            'product'        => 'Produk',
            'product-uom'    => 'UOM Produk',
            'creator'        => 'Pembuat',
            'display-type'   => 'Tipe Tampilan',
            'name'           => 'Nama',
            'quantity'       => 'Kuantitas',
        ],
    ],

    'table' => [
        'columns' => [
            'sort'           => 'Urutkan',
            'order-template' => 'Templat Pesanan',
            'company'        => 'Perusahaan',
            'product'        => 'Produk',
            'product-uom'    => 'UOM Produk',
            'created-by'     => 'Dibuat Oleh',
            'display-type'   => 'Tipe Tampilan',
            'name'           => 'Nama',
            'quantity'       => 'Kuantitas',
            'created-at'     => 'Dibuat Pada',
            'updated-at'     => 'Diperbarui Pada',

        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Produk Templat Pesanan diperbarui',
                    'body'  => 'Produk templat pesanan berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Produk Templat Pesanan dihapus',
                    'body'  => 'Produk templat pesanan berhasil dihapus.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Produk Templat Pesanan dihapus',
                    'body'  => 'Produk templat pesanan berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sort'           => 'Urutan Sortir',
            'order-template' => 'Templat Pesanan',
            'company'        => 'Perusahaan',
            'product'        => 'Produk',
            'product-uom'    => 'UOM Produk',
            'display-type'   => 'Tipe Tampilan',
            'name'           => 'Nama',
            'quantity'       => 'Kuantitas',
        ],
    ],
];
