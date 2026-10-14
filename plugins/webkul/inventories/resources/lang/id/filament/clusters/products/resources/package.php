<?php

return [
    'navigation' => [
        'title' => 'Paket',
        'group' => 'Persediaan',
    ],

    'global-search' => [
        'name'         => 'Nama',
        'package-type' => 'Tipe Paket',
        'location'     => 'Lokasi',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'name'             => 'Nama',
                    'name-placeholder' => 'mis. PACK007',
                    'package-type'     => 'Tipe Paket',
                    'pack-date'        => 'Tanggal Kemas',
                    'location'         => 'Lokasi',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nama',
            'package-type' => 'Tipe Paket',
            'location'     => 'Lokasi',
            'company'      => 'Perusahaan',
            'created-at'   => 'Dibuat Pada',
            'updated-at'   => 'Diperbarui Pada',
        ],

        'groups' => [
            'package-type'   => 'Tipe Paket',
            'location'       => 'Lokasi',
            'created-at'     => 'Dibuat Pada',
        ],

        'filters' => [
            'package-type' => 'Tipe Paket',
            'location'     => 'Lokasi',
            'creator'      => 'Pembuat',
            'company'      => 'Perusahaan',
        ],

        'actions' => [
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

        'bulk-actions' => [
            'print-without-content' => [
                'label' => 'Cetak Barcode',
            ],

            'print-with-content' => [
                'label' => 'Cetak Kode Batang Beserta Konten',
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
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Rincian Paket',

                'entries' => [
                    'name'         => 'Nama Paket',
                    'package-type' => 'Tipe Paket',
                    'pack-date'    => 'Tanggal Kemas',
                    'location'     => 'Lokasi',
                    'company'      => 'Perusahaan',
                    'created-at'   => 'Dibuat Pada',
                    'updated-at'   => 'Terakhir Diperbarui',
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
