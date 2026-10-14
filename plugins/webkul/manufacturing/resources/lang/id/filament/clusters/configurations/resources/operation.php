<?php

return [
    'navigation' => [
        'title' => 'Operasi',
        'group' => 'Konfigurasi',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',
                'fields' => [
                    'name'              => 'Operasi',
                    'name-placeholder'  => 'mis. Pemotongan',
                    'bill-of-material'  => 'Daftar Material',
                    'work-center'       => 'Pusat Kerja',
                    'apply-on-variants' => 'Terapkan pada Varian',
                    'company'           => 'Perusahaan',
                    'blocked-by'        => 'Diblokir Oleh',
                ],
            ],
            'settings' => [
                'title'  => 'Pengaturan',
                'fields' => [
                    'time-mode'                  => 'Perhitungan Durasi',
                    'time-mode-batch'            => 'Berdasarkan',
                    'time-mode-batch-prefix'     => 'terakhir',
                    'time-mode-batch-suffix'     => 'perintah kerja',
                    'manual-cycle-time'          => 'Durasi Bawaan',
                    'manual-cycle-time-suffix'   => 'menit',
                ],
            ],
            'worksheet' => [
                'title'  => 'Lembar Kerja',
                'fields' => [
                    'worksheet'                => 'Lembar Kerja',
                    'pdf'                      => 'PDF',
                    'google-slide'             => 'Google Slide',
                    'google-slide-placeholder' => 'Tautan Google Slide',
                    'description'              => 'Deskripsi',
                    'description-placeholder'  => 'Deskripsi operasi...',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'              => 'Operasi',
            'bill-of-material'  => 'Daftar Material',
            'work-center'       => 'Pusat Kerja',
            'time-mode'         => 'Perhitungan Durasi',
            'manual-cycle-time' => 'Durasi Bawaan',
            'worksheet-type'    => 'Lembar Kerja',
            'deleted-at'        => 'Dihapus Pada',
            'created-at'        => 'Dibuat Pada',
            'updated-at'        => 'Diperbarui Pada',
        ],
        'filters' => [
            'work-center'    => 'Pusat Kerja',
            'time-mode'      => 'Perhitungan Durasi',
            'worksheet-type' => 'Lembar Kerja',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operasi dipulihkan',
                    'body'  => 'Operasi berhasil dipulihkan.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operasi diarsipkan',
                    'body'  => 'Operasi berhasil diarsipkan.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operasi dihapus',
                        'body'  => 'Operasi dihapus secara permanen.',
                    ],
                    'error' => [
                        'title' => 'Operasi tidak dapat dihapus',
                        'body'  => 'Operasi tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operasi dipulihkan',
                    'body'  => 'Operasi yang dipilih berhasil dipulihkan.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operasi diarsipkan',
                    'body'  => 'Operasi yang dipilih berhasil diarsipkan.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operasi dihapus',
                        'body'  => 'Operasi yang dipilih telah dihapus secara permanen.',
                    ],
                    'error' => [
                        'title' => 'Operasi tidak dapat dihapus',
                        'body'  => 'Satu atau beberapa operasi yang dipilih sedang digunakan.',
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
                    'name'              => 'Operasi',
                    'bill-of-material'  => 'Daftar Material',
                    'work-center'       => 'Pusat Kerja',
                    'apply-on-variants' => 'Terapkan pada Varian',
                    'company'           => 'Perusahaan',
                ],
            ],
            'settings' => [
                'title'   => 'Pengaturan',
                'entries' => [
                    'time-mode'                => 'Perhitungan Durasi',
                    'time-mode-batch'          => 'Berdasarkan',
                    'manual-cycle-time'        => 'Durasi Bawaan',
                    'manual-cycle-time-suffix' => 'menit',
                ],
            ],
            'worksheet' => [
                'title'   => 'Lembar Kerja',
                'entries' => [
                    'worksheet'    => 'Lembar Kerja',
                    'pdf'          => 'PDF',
                    'google-slide' => 'Google Slide',
                    'description'  => 'Deskripsi',
                ],
            ],
            'record-information' => [
                'title'   => 'Informasi Rekaman',
                'entries' => [
                    'created-by'   => 'Dibuat Oleh',
                    'created-at'   => 'Dibuat Pada',
                    'last-updated' => 'Terakhir Diperbarui',
                ],
            ],
        ],
    ],
];
