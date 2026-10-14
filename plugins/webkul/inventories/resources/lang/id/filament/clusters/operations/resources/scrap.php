<?php

return [
    'navigation' => [
        'title' => 'Afkir',
        'group' => 'Penyesuaian',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'product'              => 'Produk',
                    'package'              => 'Paket',
                    'quantity'             => 'Kuantitas',
                    'unit'                 => 'Satuan ukur',
                    'lot'                  => 'Lot/Serial',
                    'tags'                 => 'Tag',
                    'name'                 => 'Nama',
                    'color'                => 'Warna',
                    'owner'                => 'Pemilik',
                    'source-location'      => 'Lokasi Sumber',
                    'destination-location' => 'Lokasi Barang Afkir',
                    'source-document'      => 'Dokumen Sumber',
                    'company'              => 'Perusahaan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Tanggal',
            'reference'       => 'Referensi',
            'product'         => 'Produk',
            'package'         => 'Paket',
            'quantity'        => 'Kuantitas',
            'uom'             => 'Satuan ukur',
            'source-location' => 'Lokasi Sumber',
            'scrap-location'  => 'Lokasi Barang Afkir',
            'unit'            => 'Satuan ukur',
            'lot'             => 'Lot/Serial',
            'tags'            => 'Tag',
            'state'           => 'Status',
        ],

        'groups' => [
            'product'              => 'Produk',
            'source-location'      => 'Lokasi Sumber',
            'destination-location' => 'Lokasi Barang Afkir',
        ],

        'filters' => [
            'source-location'      => 'Lokasi Sumber',
            'destination-location' => 'Lokasi Barang Afkir',
            'product'              => 'Produk',
            'state'                => 'Status',
            'product-category'     => 'Kategori Produk',
            'uom'                  => 'Satuan ukur',
            'lot'                  => 'Lot/Serial',
            'package'              => 'Paket',
            'tags'                 => 'Tag',
            'company'              => 'Perusahaan',
            'quantity'             => 'Kuantitas',
            'creator'              => 'Pembuat',
            'closed-at'            => 'Ditutup Pada',
            'created-at'           => 'Dibuat Pada',
            'updated-at'           => 'Diperbarui Pada',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Afkir dihapus',
                        'body'  => 'Afkir berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Afkir tidak dapat dihapus',
                        'body'  => 'Afkir tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Barang afkir dihapus',
                        'body'  => 'Afkir yang dipilih berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Afkir tidak dapat dihapus',
                        'body'  => 'Afkir tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Detail Afkir',

                'entries' => [
                    'product'              => 'Produk',
                    'quantity'             => 'Kuantitas',
                    'lot'                  => 'Lot',
                    'tags'                 => 'Tag',
                    'package'              => 'Paket',
                    'owner'                => 'Pemilik',
                    'source-location'      => 'Lokasi Sumber',
                    'destination-location' => 'Lokasi Barang Afkir',
                    'source-document'      => 'Dokumen Sumber',
                    'company'              => 'Perusahaan',
                ],
            ],

            'record-information' => [
                'title' => 'Informasi Rekaman',

                'entries' => [
                    'created-by'   => 'Dibuat Oleh',
                    'created-at'   => 'Dibuat Pada',
                    'last-updated' => 'Terakhir Diperbarui',
                ],
            ],
        ],
    ],
];
