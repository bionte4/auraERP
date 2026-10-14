<?php

return [
    'navigation' => [
        'title' => 'Proyek',
    ],

    'global-search' => [
        'project-manager' => 'Manajer Proyek',
        'customer'        => 'Pelanggan',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'name'             => 'Nama',
                    'name-placeholder' => 'Nama Proyek...',
                    'description'      => 'Deskripsi',
                ],
            ],

            'additional' => [
                'title' => 'Informasi Tambahan',

                'fields' => [
                    'project-manager'             => 'Manajer Proyek',
                    'customer'                    => 'Pelanggan',
                    'start-date'                  => 'Tanggal Mulai',
                    'end-date'                    => 'Tanggal Akhir',
                    'allocated-hours'             => 'Jam yang Dialokasikan',
                    'allocated-hours-helper-text' => 'Dalam jam (Mis. 1.5 jam berarti 1 jam 30 menit)',
                    'tags'                        => 'Tag',
                    'company'                     => 'Perusahaan',
                ],
            ],

            'settings' => [
                'title' => 'Pengaturan',

                'fields' => [
                    'visibility'                   => 'Visibilitas',
                    'visibility-hint-tooltip'      => 'Izinkan karyawan mengakses proyek atau tugas Anda dengan menambahkan mereka sebagai pengikut. Mereka akan secara otomatis memperoleh akses ke setiap tugas yang ditugaskan kepada mereka..',
                    'private-description'          => 'Hanya pengguna internal yang diundang.',
                    'internal-description'         => 'Semua pengguna internal dapat melihat.',
                    'public-description'           => 'Pengguna portal yang diundang dan semua pengguna internal.',
                    'time-management'              => 'Manajemen Waktu',
                    'allow-timesheets'             => 'Izinkan Lembar Waktu',
                    'allow-timesheets-helper-text' => 'Catat waktu pada tugas dan lacak kemajuan',
                    'task-management'              => 'Manajemen Tugas',
                    'allow-milestones'             => 'Izinkan Tonggak Pencapaian',
                    'allow-milestones-helper-text' => 'Pantau tonggak utama yang penting untuk mencapai keberhasilan.',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Nama',
            'customer'        => 'Pelanggan',
            'start-date'      => 'Tanggal Mulai',
            'end-date'        => 'Tanggal Akhir',
            'planned-date'    => 'Tanggal Rencana',
            'remaining-hours' => 'Jam Tersisa',
            'project-manager' => 'Manajer Proyek',
        ],

        'groups' => [
            'stage'           => 'Tahap',
            'project-manager' => 'Manajer Proyek',
            'customer'        => 'Pelanggan',
            'created-at'      => 'Dibuat Pada',
        ],

        'filters' => [
            'name'             => 'Nama',
            'visibility'       => 'Visibilitas',
            'start-date'       => 'Tanggal Mulai',
            'end-date'         => 'Tanggal Akhir',
            'allow-timesheets' => 'Izinkan Lembar Waktu',
            'allow-milestones' => 'Izinkan Tonggak Pencapaian',
            'allocated-hours'  => 'Jam yang Dialokasikan',
            'created-at'       => 'Dibuat Pada',
            'updated-at'       => 'Diperbarui Pada',
            'stage'            => 'Tahap',
            'customer'         => 'Pelanggan',
            'project-manager'  => 'Manajer Proyek',
            'company'          => 'Perusahaan',
            'creator'          => 'Pembuat',
            'tags'             => 'Tag',
        ],

        'actions' => [
            'tasks'      => ':count Tugas',
            'milestones' => ':completed tonggak pencapaian selesai dari :all',

            'restore' => [
                'notification' => [
                    'title' => 'Proyek dipulihkan',
                    'body'  => 'Proyek berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Proyek dihapus',
                    'body'  => 'Proyek berhasil dihapus.',
                ],
            ],

            'force-delete' => [

                'notification' => [

                    'success' => [
                        'title' => 'Proyek dihapus secara permanen',
                        'body'  => 'Proyek berhasil dihapus secara permanen.',
                    ],

                    'error' => [
                        'title' => 'Proyek tidak dapat dihapus secara permanen',
                        'body'  => 'Proyek ini terkait dengan data lain.',
                    ],

                ],
            ],

        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'entries' => [
                    'name'             => 'Nama',
                    'name-placeholder' => 'Nama Proyek...',
                    'description'      => 'Deskripsi',
                ],
            ],

            'additional' => [
                'title' => 'Informasi Tambahan',

                'entries' => [
                    'project-manager'        => 'Manajer Proyek',
                    'customer'               => 'Pelanggan',
                    'project-timeline'       => 'Linimasa Proyek',
                    'allocated-hours'        => 'Jam yang Dialokasikan',
                    'allocated-hours-suffix' => ' Jam',
                    'remaining-hours'        => 'Jam Tersisa',
                    'remaining-hours-suffix' => ' Jam',
                    'current-stage'          => 'Tahap Saat Ini',
                    'tags'                   => 'Tag',
                ],
            ],

            'statistics' => [
                'title' => 'Statistik',

                'entries' => [
                    'total-tasks'         => 'Total Tugas',
                    'milestones-progress' => 'Progres Tonggak',
                ],
            ],

            'record-information' => [
                'title' => 'Informasi Rekaman',

                'entries' => [
                    'created-at'   => 'Dibuat Pada',
                    'created-by'   => 'Dibuat Oleh',
                    'last-updated' => 'Terakhir Diperbarui',
                ],
            ],

            'settings' => [
                'title' => 'Pengaturan Proyek',

                'entries' => [
                    'visibility'         => 'Visibilitas',
                    'timesheets-enabled' => 'Lembar Waktu Diaktifkan',
                    'milestones-enabled' => 'Tonggak Diaktifkan',
                ],
            ],
        ],
    ],
];
