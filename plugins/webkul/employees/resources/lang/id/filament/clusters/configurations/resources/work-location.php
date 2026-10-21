<?php

return [
    'title' => 'Lokasi Kerja',

    'navigation' => [
        'title' => 'Lokasi Kerja',
        'group' => 'Karyawan',
    ],

    'form' => [
        'name'               => 'Nama',
        'company'            => 'Perusahaan',
        'location-type'      => 'Tipe Lokasi',
        'location-number'    => 'Nomor Lokasi',
        'city'               => 'Kota',
        'city-help'          => 'Pilih kabupaten/kota Indonesia. Koordinat peta Welcome terisi otomatis (bisa disesuaikan).',
        'latitude'           => 'Latitude',
        'longitude'          => 'Longitude',
        'coordinates-help'   => 'Terisi dari kota. Diperlukan agar muncul di peta Welcome. Absensi geofence tetap memakai Lokasi kerja (Attendance Sites).',
        'status'             => 'Status',
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'name'            => 'Nama',
            'status'          => 'Status',
            'company'         => 'Perusahaan',
            'location-type'   => 'Tipe Lokasi',
            'location-number' => 'Nomor Lokasi',
            'city'            => 'Kota',
            'deleted-at'      => 'Dihapus Pada',
            'created-by'      => 'Dibuat Oleh',
            'created-at'      => 'Dibuat Pada',
            'updated-at'      => 'Diperbarui Pada',
        ],

        'filters' => [
            'name'            => 'Nama',
            'status'          => 'Status',
            'created-by'      => 'Dibuat Oleh',
            'company'         => 'Perusahaan',
            'location-number' => 'Nomor Lokasi',
            'location-type'   => 'Tipe Lokasi',
            'updated-at'      => 'Diperbarui Pada',
            'created-at'      => 'Dibuat Pada',
        ],

        'groups' => [
            'name'          => 'Nama',
            'status'        => 'Status',
            'location-type' => 'Tipe Lokasi',
            'company'       => 'Perusahaan',
            'created-by'    => 'Dibuat Oleh',
            'created-at'    => 'Dibuat Pada',
            'updated-at'    => 'Diperbarui Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Lokasi Kerja diperbarui',
                    'body'  => 'Lokasi kerja berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Lokasi Kerja dipulihkan',
                    'body'  => 'Lokasi kerja berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lokasi Kerja dihapus',
                    'body'  => 'Lokasi kerja berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Lokasi Kerja dihapus permanen',
                    'body'  => 'Lokasi kerja berhasil dihapus paksa.',
                ],
            ],

            'empty-state' => [
                'notification' => [
                    'title' => 'Lokasi Kerja dibuat',
                    'body'  => 'Lokasi kerja berhasil dibuat.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Lokasi Kerja dihapus',
                    'body'  => 'Lokasi kerja berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Lokasi Kerja dihapus permanen',
                    'body'  => 'Lokasi kerja berhasil dihapus paksa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'            => 'Nama',
        'company'         => 'Perusahaan',
        'location-type'   => 'Tipe Lokasi',
        'location-number' => 'Nomor Lokasi',
        'city'            => 'Kota',
        'latitude'        => 'Latitude',
        'longitude'       => 'Longitude',
        'status'          => 'Status',
    ],
];
