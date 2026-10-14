<?php

return [
    'navigation' => [
        'title' => 'Aturan Penempatan',
        'group' => 'Manajemen Gudang',
    ],

    'form' => [
        'fields' => [
            'in-location'          => 'Saat Produk Tiba di',
            'product'              => 'Produk',
            'product-placeholder'  => 'Semua Produk',
            'category'             => 'Kategori Produk',
            'category-placeholder' => 'Semua Kategori',
            'storage-category'     => 'Kategori Penyimpanan',
            'out-location'         => 'Simpan Ke',
            'sub-location'         => 'Sub Lokasi',
            'company'              => 'Perusahaan',
        ],
    ],

    'table' => [
        'columns' => [
            'in-location'      => 'Saat Produk Tiba di',
            'product'          => 'Produk',
            'category'         => 'Kategori Produk',
            'storage-category' => 'Kategori Penyimpanan',
            'out-location'     => 'Simpan Ke',
            'sub-location'     => 'Sub Lokasi',
            'company'          => 'Perusahaan',
            'deleted-at'       => 'Dihapus Pada',
            'created-at'       => 'Dibuat Pada',
            'updated-at'       => 'Diperbarui Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Aturan penempatan diperbarui',
                    'body'  => 'Aturan penempatan berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Aturan penempatan dipulihkan',
                    'body'  => 'Aturan penempatan berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Aturan penempatan dihapus',
                    'body'  => 'Aturan penempatan berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Aturan penempatan tidak dapat dihapus',
                        'body'  => 'Aturan penempatan tidak dapat dihapus secara permanen karena dirujuk oleh data lain.',
                    ],

                    'success' => [
                        'title' => 'Aturan penempatan dihapus secara permanen',
                        'body'  => 'Aturan penempatan berhasil dihapus secara permanen.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Aturan penempatan dipulihkan',
                    'body'  => 'Aturan penempatan berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Aturan penempatan dihapus',
                    'body'  => 'Aturan penempatan berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Aturan penempatan tidak dapat dihapus',
                        'body'  => 'Beberapa aturan penempatan tidak dapat dihapus secara permanen karena dirujuk oleh data lain.',
                    ],

                    'success' => [
                        'title' => 'Aturan penempatan dihapus secara permanen',
                        'body'  => 'Aturan penempatan berhasil dihapus secara permanen.',
                    ],
                ],
            ],
        ],
    ],
];
