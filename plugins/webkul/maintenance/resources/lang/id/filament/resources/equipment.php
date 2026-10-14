<?php

return [
    'navigation' => [
        'title' => 'Peralatan',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Informasi Umum',
                'fields' => [
                    'name' => 'Nama',
                    'note' => 'Deskripsi',
                ],
            ],

            'settings' => [
                'title'  => 'Pengaturan',
                'fields' => [
                    'category'   => 'Kategori Peralatan',
                    'team'       => 'Tim Pemeliharaan',
                    'company'    => 'Perusahaan',
                    'technician' => 'Teknisi',
                    'owner'      => 'Pemilik (User)',
                    'employee'   => 'Karyawan Pemegang Aset',
                    'project'    => 'Proyek',
                    'customer'   => 'Customer / Klien',
                    'location'   => 'Digunakan di lokasi',
                ],
            ],

            'product-information' => [
                'title'  => 'Informasi Produk',
                'fields' => [
                    'partner'                     => 'Pemasok',
                    'partner-ref'                 => 'Referensi Pemasok',
                    'model'                       => 'Model',
                    'serial-no'                   => 'Nomor Seri',
                    'effective-date'              => 'Tanggal Efektif',
                    'effective-date-hint-tooltip' => 'Digunakan sebagai titik awal untuk menghitung waktu rata-rata antar kegagalan.',
                    'cost'                        => 'Biaya',
                    'warranty-date'               => 'Tanggal Kedaluwarsa Garansi',
                ],
            ],

            'maintenance' => [
                'title'  => 'Pemeliharaan',
                'fields' => [
                    'expected-mtbf' => 'Perkiraan Waktu Rata-rata Antarkegagalan',
                ],
                'suffixes' => [
                    'days' => 'hari',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama Aset / Peralatan',
            'owner'      => 'Pemilik',
            'employee'   => 'Karyawan',
            'project'    => 'Proyek',
            'customer'   => 'Customer',
            'serial-no'  => 'Nomor Seri',
            'category'   => 'Kategori Peralatan',
            'technician' => 'Teknisi',
            'company'    => 'Perusahaan',
            'created-at' => 'Dibuat Pada',
        ],

        'filters' => [
            'category'   => 'Kategori Peralatan',
            'team'       => 'Tim Pemeliharaan',
            'technician' => 'Teknisi',
            'employee'   => 'Karyawan',
            'project'    => 'Proyek',
            'customer'   => 'Customer',
        ],

        'groups' => [
            'category'   => 'Kategori Peralatan',
            'owner'      => 'Pemilik',
            'employee'   => 'Karyawan',
            'project'    => 'Proyek',
            'customer'   => 'Customer',
            'technician' => 'Teknisi',
            'vendor'     => 'Pemasok',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Peralatan diperbarui',
                    'body'  => 'Peralatan berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Peralatan dipulihkan',
                    'body'  => 'Peralatan berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Peralatan diarsipkan',
                    'body'  => 'Peralatan berhasil diarsipkan.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Peralatan dihapus',
                        'body'  => 'Peralatan dihapus secara permanen.',
                    ],

                    'error' => [
                        'title' => 'Peralatan tidak dapat dihapus',
                        'body'  => 'Peralatan ini dirujuk oleh data lain.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Peralatan dipulihkan',
                    'body'  => 'Peralatan yang dipilih berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Peralatan diarsipkan',
                    'body'  => 'Peralatan yang dipilih berhasil diarsipkan.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Peralatan dibuat',
                    'body'  => 'Peralatan berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informasi Umum',
                'entries' => [
                    'name' => 'Nama',
                    'note' => 'Deskripsi',
                ],
            ],

            'settings' => [
                'title'   => 'Pengaturan',
                'entries' => [
                    'category'   => 'Kategori Peralatan',
                    'team'       => 'Tim Pemeliharaan',
                    'company'    => 'Perusahaan',
                    'technician' => 'Teknisi',
                    'owner'      => 'Pemilik (User)',
                    'employee'   => 'Karyawan Pemegang Aset',
                    'project'    => 'Proyek',
                    'customer'   => 'Customer / Klien',
                    'location'   => 'Digunakan di lokasi',
                ],
            ],

            'product-information' => [
                'title'   => 'Informasi Produk',
                'entries' => [
                    'partner'        => 'Pemasok',
                    'partner-ref'    => 'Referensi Pemasok',
                    'model'          => 'Model',
                    'serial-no'      => 'Nomor Seri',
                    'effective-date' => 'Tanggal Efektif',
                    'cost'           => 'Biaya',
                    'warranty-date'  => 'Tanggal Kedaluwarsa Garansi',
                ],
            ],

            'maintenance' => [
                'title'   => 'Pemeliharaan',
                'entries' => [
                    'expected-mtbf'          => 'Perkiraan Waktu Rata-rata Antarkegagalan',
                    'maintenance-count'      => 'Jumlah Pemeliharaan',
                    'maintenance-open-count' => 'Jumlah Pemeliharaan Terbuka',
                    'assigned-at'            => 'Tanggal Penugasan',
                    'scraped-at'             => 'Tanggal Afkir',
                ],
                'suffixes' => [
                    'days' => 'hari',
                ],
            ],
        ],
    ],

    'movements' => [
        'title'         => 'Riwayat Pergerakan / Perpindahan Aset',
        'model'         => 'Pergerakan Aset',
        'create-button' => 'Catat Pergerakan / Pindah Aset',
        'fields'        => [
            'movement-type'         => 'Jenis Pergerakan',
            'movement-date'         => 'Tanggal Pergerakan',
            'reference'             => 'No. Referensi / BAST',
            'replacement-equipment' => 'Aset Pengganti (Unit Baru)',
            'to-employee'           => 'Karyawan Tujuan',
            'to-project'            => 'Proyek Tujuan',
            'to-customer'           => 'Customer Tujuan',
            'to-location'           => 'Lokasi Tujuan',
            'notes'                 => 'Catatan / Alasan',
        ],
        'columns' => [
            'from-employee' => 'Dari Karyawan',
            'to-employee'   => 'Ke Karyawan',
            'from-project'  => 'Dari Proyek',
            'to-project'    => 'Ke Proyek',
            'from-location' => 'Dari Lokasi',
            'to-location'   => 'Ke Lokasi',
        ],
        'log' => [
            'employee'    => 'Karyawan: :from → :to',
            'project'     => 'Proyek: :from → :to',
            'location'    => 'Lokasi: :from → :to',
            'replacement' => 'Digantikan oleh unit: :equipment',
        ],
    ],
];
