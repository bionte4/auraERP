<?php

return [
    'title' => 'Kapasitas Berdasarkan Paket',

    'form' => [
        'package-type' => 'Tipe Paket',
        'qty'          => 'Kuantitas',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Tambah Kapasitas Jenis Paket',

                'notification' => [
                    'title' => 'Kapasitas Jenis Paket dibuat',
                    'body'  => 'Kapasitas jenis kemasan berhasil ditambahkan.',
                ],
            ],
        ],

        'columns' => [
            'package-type' => 'Tipe Paket',
            'qty'          => 'Kuantitas',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kapasitas Jenis Paket diperbarui',
                    'body'  => 'Kapasitas jenis kemasan berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kapasitas Jenis Paket dihapus',
                    'body'  => 'Kapasitas jenis kemasan berhasil dihapus.',
                ],
            ],
        ],
    ],
];
