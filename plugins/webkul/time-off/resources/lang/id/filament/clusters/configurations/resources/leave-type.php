<?php

return [
    'title'      => 'Jenis Cuti',
    'navigation' => [
        'title' => 'Jenis Cuti',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Informasi Umum',
                'fields' => [
                    'name'                => 'Judul',
                    'approval'            => 'Persetujuan',
                    'requires-allocation' => 'Memerlukan Alokasi',
                    'employee-requests'   => 'Permintaan Karyawan',
                    'display-option'      => 'Opsi Tampilan',
                ],
            ],
            'display-option' => [
                'title'  => 'Opsi Tampilan',
                'fields' => [
                    'color' => 'Warna',
                ],
            ],
            'configuration' => [
                'title' => 'Konfigurasi',

                'fields' => [
                    'notified-time-off-officers'          => 'Petugas Cuti yang Diberitahu',
                    'take-time-off-in'                    => 'Ambil Cuti Dalam',
                    'public-holiday-included'             => 'Termasuk Hari Libur Nasional',
                    'allow-to-attach-supporting-document' => 'Izinkan Melampirkan Dokumen Pendukung',
                    'show-on-dashboard'                   => 'Tampilkan di Dasbor',
                    'allow-negative-cap'                  => 'Izinkan Batas Negatif',
                    'kind-off-time'                       => 'Jenis Waktu',
                    'max-negative-cap'                    => 'Batas Negatif Maksimum',
                    'kind-of-time'                        => 'Jenis Cuti',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                      => 'Nama',
            'company-name'              => 'Perusahaan',
            'color'                     => 'Warna',
            'notified-time-officers'    => 'Petugas Waktu yang Diberitahu',
            'time-off-approval'         => 'Persetujuan Cuti',
            'requires-allocation'       => 'Memerlukan Alokasi',
            'allocation-approval'       => 'Persetujuan Alokasi',
            'employee-request'          => 'Permintaan Karyawan',
        ],

        'filters' => [
            'name'                => 'Nama',
            'company-name'        => 'Perusahaan',
            'time-off-approval'   => 'Persetujuan Cuti',
            'requires-allocation' => 'Memerlukan Alokasi',
            'time-type'           => 'Jenis Waktu',
            'request-unit'        => 'Unit Permintaan',
            'created-by'          => 'Dibuat Oleh',
            'employee-request'    => 'Permintaan Karyawan',
            'created-at'          => 'Dibuat Pada',
            'updated-at'          => 'Diperbarui Pada',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Jenis Cuti dihapus',
                    'body'  => 'Jenis cuti berhasil dihapus.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Jenis Cuti dipulihkan',
                    'body'  => 'Jenis cuti berhasil dipulihkan.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Jenis Cuti dipulihkan',
                    'body'  => 'Jenis cuti berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jenis Cuti dihapus',
                    'body'  => 'Jenis cuti berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Jenis Cuti dihapus permanen',
                        'body'  => 'Jenis cuti berhasil dihapus paksa.',
                    ],
                    'error' => [
                        'title' => 'Jenis Cuti tidak dapat dihapus',
                        'body'  => 'Jenis cuti tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informasi Umum',
                'entries' => [
                    'name'                => 'Judul',
                    'approval'            => 'Persetujuan',
                    'requires-allocation' => 'Memerlukan Alokasi',
                    'employee-requests'   => 'Permintaan Karyawan',
                    'display-option'      => 'Opsi Tampilan',
                ],
            ],
            'display-option' => [
                'title'   => 'Opsi Tampilan',
                'entries' => [
                    'color' => 'Warna',
                ],
            ],
            'configuration' => [
                'title' => 'Konfigurasi',

                'entries' => [
                    'notified-time-off-officers'          => 'Petugas Cuti yang Diberitahu',
                    'take-time-off-in'                    => 'Ambil Cuti Dalam',
                    'public-holiday-included'             => 'Termasuk Hari Libur Nasional',
                    'allow-to-attach-supporting-document' => 'Izinkan Melampirkan Dokumen Pendukung',
                    'show-on-dashboard'                   => 'Tampilkan di Dasbor',
                    'kind-off-time'                       => 'Jenis Waktu',
                    'max-negative-cap'                    => 'Batas Negatif Maksimum',
                    'kind-of-time'                        => 'Jenis Cuti',
                ],
            ],
        ],
    ],
];
