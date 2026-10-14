<?php

return [
    'navigation' => [
        'group' => 'Pemeliharaan',
        'title' => 'Permintaan Pemeliharaan',
    ],

    'form' => [
        'sections' => [
            'request' => [
                'title'  => 'Permintaan',
                'fields' => [
                    'name'                      => 'Permintaan',
                    'name-placeholder'          => 'mis. Layar tidak berfungsi',
                    'equipment'                 => 'Peralatan',
                    'category'                  => 'Kategori',
                    'requested-at'              => 'Tanggal Permintaan',
                    'requested-at-hint-tooltip' => 'Tanggal permintaan pemeliharaan dilaporkan.',
                    'maintenance-type'          => 'Jenis Pemeliharaan',
                    'recurrent'                 => 'Berulang',
                    'repeat-every'              => 'Ulangi Setiap',
                    'maintenance-type-options'  => [
                        'corrective' => 'Korektif',
                        'preventive' => 'Preventif',
                    ],
                ],
                'tabs' => [
                    'notes' => [
                        'title'  => 'Catatan',
                        'fields' => [
                            'description'             => 'Catatan Internal',
                            'description-placeholder' => 'Catatan internal',
                        ],
                    ],
                    'instructions' => [
                        'title'  => 'Instruksi',
                        'fields' => [
                            'instruction-type'         => 'Jenis Instruksi',
                            'instruction-type-options' => [
                                'pdf'          => 'PDF',
                                'google-slide' => 'Google Slide',
                                'text'         => 'Teks',
                            ],
                            'instruction-pdf'              => 'PDF',
                            'instruction-google-slide'     => 'Google Slide',
                            'instruction-text'             => 'Deskripsi',
                            'instruction-text-placeholder' => 'Deskripsi',
                        ],
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Pengaturan',
                'fields' => [
                    'team'                      => 'Tim',
                    'responsible'               => 'Penanggung Jawab',
                    'scheduled-at'              => 'Tanggal Terjadwal',
                    'scheduled-at-hint-tooltip' => 'Tanggal dan waktu pekerjaan pemeliharaan ini direncanakan untuk dimulai.',
                    'duration'                  => 'Durasi',
                    'duration-hint-tooltip'     => 'Perkiraan durasi pemeliharaan.',
                    'duration-suffix'           => 'jam',
                    'priority'                  => 'Prioritas',
                    'company'                   => 'Perusahaan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Subjek',
            'creator'    => 'Dibuat oleh Pengguna',
            'technician' => 'Teknisi',
            'category'   => 'Kategori',
            'stage'      => 'Tahap',
            'company'    => 'Perusahaan',
        ],

        'groups' => [
            'stage'       => 'Tahap',
            'assigned-to' => 'Ditugaskan kepada',
            'category'    => 'Kategori',
            'created-by'  => 'Dibuat Oleh',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Permintaan pemeliharaan dipulihkan',
                    'body'  => 'Permintaan pemeliharaan berhasil dipulihkan.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Permintaan pemeliharaan diarsipkan',
                    'body'  => 'Permintaan pemeliharaan berhasil diarsipkan.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Permintaan pemeliharaan dihapus',
                        'body'  => 'Permintaan pemeliharaan telah dihapus secara permanen.',
                    ],
                    'error' => [
                        'title' => 'Permintaan pemeliharaan tidak dapat dihapus',
                        'body'  => 'Permintaan pemeliharaan ini dirujuk oleh data lain.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Permintaan pemeliharaan dipulihkan',
                    'body'  => 'Permintaan pemeliharaan yang dipilih berhasil dipulihkan.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Permintaan pemeliharaan diarsipkan',
                    'body'  => 'Permintaan pemeliharaan yang dipilih berhasil diarsipkan.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'request' => [
                'title'   => 'Permintaan',
                'entries' => [
                    'name'                     => 'Permintaan',
                    'equipment'                => 'Peralatan',
                    'category'                 => 'Kategori',
                    'requested-at'             => 'Tanggal Permintaan',
                    'maintenance-type'         => 'Jenis Pemeliharaan',
                    'instruction-type'         => 'Jenis Instruksi',
                    'instruction-pdf'          => 'PDF',
                    'instruction-google-slide' => 'Google Slide',
                    'description'              => 'Catatan Internal',
                    'instruction-text'         => 'Deskripsi',
                ],
            ],

            'settings' => [
                'title'   => 'Pengaturan',
                'entries' => [
                    'team'            => 'Tim',
                    'responsible'     => 'Penanggung Jawab',
                    'scheduled-at'    => 'Tanggal Terjadwal',
                    'duration'        => 'Durasi',
                    'duration-suffix' => 'jam',
                    'priority'        => 'Prioritas',
                    'company'         => 'Perusahaan',
                ],
            ],
        ],
    ],
];
