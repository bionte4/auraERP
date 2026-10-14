<?php

return [
    'navigation' => [
        'title' => 'Tipe Paket',
        'group' => 'Pengiriman',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',

                'fields' => [
                    'name'       => 'Nama',
                    'barcode'    => 'Barcode',
                    'company'    => 'Perusahaan',
                    'weight'     => 'Berat',
                    'max-weight' => 'Berat Maksimum',

                    'fieldsets' => [
                        'size' => [
                            'title' => 'Ukuran',

                            'fields' => [
                                'length' => 'Panjang',
                                'width'  => 'Lebar',
                                'height' => 'Tinggi',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'barcode'    => 'Barcode',
            'weight'     => 'Berat',
            'max-weight' => 'Berat Maksimum',
            'width'      => 'Lebar',
            'height'     => 'Tinggi',
            'length'     => 'Panjang',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'groups' => [
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Jenis Paket dihapus',
                    'body'  => 'Jenis paket berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Jenis Paket dihapus',
                    'body'  => 'Jenis paket berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informasi Umum',
                'entries' => [
                    'name'      => 'Nama',
                    'fieldsets' => [
                        'size' => [
                            'title'   => 'Dimensi Paket',
                            'entries' => [
                                'length' => 'Panjang',
                                'width'  => 'Lebar',
                                'height' => 'Tinggi',
                            ],
                        ],
                    ],
                    'weight'     => 'Berat Dasar',
                    'max-weight' => 'Berat Maksimum',
                    'barcode'    => 'Barcode',
                    'company'    => 'Perusahaan',
                    'created-at' => 'Dibuat Pada',
                    'updated-at' => 'Terakhir Diperbarui',
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
