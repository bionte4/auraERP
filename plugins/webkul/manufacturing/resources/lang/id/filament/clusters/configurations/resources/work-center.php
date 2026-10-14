<?php

return [
    'navigation' => [
        'title' => 'Pusat Kerja',
        'group' => 'Konfigurasi',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',
                'fields' => [
                    'name'                     => 'Nama',
                    'name-placeholder'         => 'mis. Lini Perakitan 1',
                    'code'                     => 'Kode',
                    'code-placeholder'         => 'mis. AL1',
                    'working-state'            => 'Status Kerja',
                    'color'                    => 'Warna',
                    'tags'                     => 'Tag',
                    'alternative-work-centers' => 'Pusat Kerja Alternatif',
                    'company'                  => 'Perusahaan',
                    'calendar'                 => 'Jam Kerja',
                ],
            ],

            'information' => [
                'title'     => 'Informasi Umum',
                'fieldsets' => [
                    'production-information' => 'Informasi Produksi',
                    'costing-information'    => 'Informasi Biaya',
                ],
                'fields' => [
                    'default-capacity' => 'Kapasitas Bawaan',
                    'time-efficiency'  => 'Efisiensi Waktu',
                    'oee-target'       => 'Target OEE',
                    'costs-per-hour'   => 'Biaya per Jam',
                    'cost-suffix'      => 'per jam',
                    'setup-time'       => 'Waktu Persiapan',
                    'cleanup-time'     => 'Waktu Pembersihan',
                    'time-suffix'      => 'menit',
                ],
            ],

            'description' => [
                'title'  => 'Deskripsi',
                'fields' => [
                    'note'             => 'Deskripsi',
                    'note-placeholder' => 'Deskripsi pusat kerja...',
                ],
            ],

            'specific-capacity' => [
                'title'  => 'Kapasitas Spesifik',
                'fields' => [
                    'records' => 'Kapasitas Spesifik',
                ],
                'columns' => [
                    'product'      => 'Produk',
                    'product-uom'  => 'UOM',
                    'capacity'     => 'Kapasitas',
                    'setup-time'   => 'Waktu Persiapan',
                    'cleanup-time' => 'Waktu Pembersihan',
                ],
                'actions' => [
                    'add' => 'Tambah baris',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Nama',
            'code'             => 'Kode',
            'company'          => 'Perusahaan',
            'calendar'         => 'Jam Kerja',
            'working-state'    => 'Status Kerja',
            'default-capacity' => 'Kapasitas',
            'time-efficiency'  => 'Efisiensi',
            'costs-per-hour'   => 'Biaya per Jam',
            'deleted-at'       => 'Dihapus Pada',
            'created-at'       => 'Dibuat Pada',
            'updated-at'       => 'Diperbarui Pada',
        ],

        'groups' => [
            'company' => 'Perusahaan',
        ],

        'filters' => [
            'company'       => 'Perusahaan',
            'working-state' => 'Status Kerja',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Pusat kerja dipulihkan',
                    'body'  => 'Pusat kerja berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Pusat kerja diarsipkan',
                    'body'  => 'Pusat kerja berhasil diarsipkan.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pusat kerja dihapus',
                        'body'  => 'Pusat kerja dihapus secara permanen.',
                    ],

                    'error' => [
                        'title' => 'Pusat kerja tidak dapat dihapus',
                        'body'  => 'Pusat kerja tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Pusat kerja dipulihkan',
                    'body'  => 'Pusat kerja yang dipilih berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Pusat kerja diarsipkan',
                    'body'  => 'Pusat kerja yang dipilih berhasil diarsipkan.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pusat kerja dihapus',
                        'body'  => 'Pusat kerja yang dipilih telah dihapus secara permanen.',
                    ],

                    'error' => [
                        'title' => 'Pusat kerja tidak dapat dihapus',
                        'body'  => 'Satu atau beberapa pusat kerja yang dipilih sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'name'                     => 'Nama Pusat Kerja',
                    'code'                     => 'Kode',
                    'working-state'            => 'Status Kerja',
                    'tags'                     => 'Tag',
                    'alternative-work-centers' => 'Pusat Kerja Alternatif',
                    'company'                  => 'Perusahaan',
                    'calendar'                 => 'Jam Kerja',
                ],
            ],

            'information' => [
                'title'     => 'Informasi Umum',
                'fieldsets' => [
                    'production-information' => 'Informasi Produksi',
                    'costing-information'    => 'Informasi Biaya',
                ],

                'entries' => [
                    'default-capacity' => 'Kapasitas Bawaan',
                    'time-efficiency'  => 'Efisiensi Waktu',
                    'oee-target'       => 'Target OEE',
                    'costs-per-hour'   => 'Biaya per Jam',
                    'cost-suffix'      => 'per pusat kerja',
                    'setup-time'       => 'Waktu Persiapan',
                    'cleanup-time'     => 'Waktu Pembersihan',
                    'time-suffix'      => 'menit',
                ],
            ],

            'description' => [
                'title'   => 'Deskripsi',
                'entries' => [
                    'note' => 'Deskripsi',
                ],
            ],

            'specific-capacity' => [
                'title'   => 'Kapasitas Spesifik',
                'columns' => [
                    'product'      => 'Produk',
                    'product-uom'  => 'UOM',
                    'capacity'     => 'Kapasitas',
                    'setup-time'   => 'Waktu Persiapan',
                    'cleanup-time' => 'Waktu Pembersihan',
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
