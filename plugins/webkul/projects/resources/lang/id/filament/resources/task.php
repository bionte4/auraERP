<?php

return [
    'title' => 'Tugas',

    'navigation' => [
        'title' => 'Tugas',
    ],

    'global-search' => [
        'project'   => 'Proyek',
        'customer'  => 'Pelanggan',
        'milestone' => 'Tonggak',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'title'             => 'Judul',
                    'title-placeholder' => 'Judul Tugas...',
                    'tags'              => 'Tag',
                    'name'              => 'Nama',
                    'color'             => 'Warna',
                    'description'       => 'Deskripsi',
                    'project'           => 'Proyek',
                    'status'            => 'Status',
                    'start_date'        => 'Tanggal Mulai',
                    'end_date'          => 'Tanggal Akhir',
                ],
            ],

            'additional' => [
                'title' => 'Informasi Tambahan',
            ],

            'settings' => [
                'title' => 'Pengaturan',

                'fields' => [
                    'project'                     => 'Proyek',
                    'milestone'                   => 'Tonggak',
                    'milestone-hint-text'         => 'Kirim layanan Anda secara otomatis saat tonggak tercapai dengan menautkannya ke item pesanan penjualan.',
                    'name'                        => 'Nama',
                    'deadline'                    => 'Batas Waktu',
                    'is-completed'                => 'Telah Selesai',
                    'customer'                    => 'Pelanggan',
                    'assignees'                   => 'Penerima Tugas',
                    'allocated-hours'             => 'Jam yang Dialokasikan',
                    'allocated-hours-helper-text' => 'Dalam jam (Mis. 1.5 jam berarti 1 jam 30 menit)',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                  => 'ID',
            'priority'            => 'Prioritas',
            'state'               => 'Status',
            'new-state'           => 'Status Baru',
            'update-state'        => 'Perbarui Status',
            'title'               => 'Judul',
            'project'             => 'Proyek',
            'project-placeholder' => 'Tugas Pribadi',
            'milestone'           => 'Tonggak',
            'customer'            => 'Pelanggan',
            'assignees'           => 'Penerima Tugas',
            'allocated-time'      => 'Waktu Dialokasikan',
            'time-spent'          => 'Waktu Terpakai',
            'time-remaining'      => 'Sisa Waktu',
            'progress'            => 'Progres',
            'deadline'            => 'Batas Waktu',
            'tags'                => 'Tag',
            'stage'               => 'Tahap',
        ],

        'groups' => [
            'state'      => 'Status',
            'project'    => 'Proyek',
            'milestone'  => 'Tonggak',
            'customer'   => 'Pelanggan',
            'deadline'   => 'Batas Waktu',
            'stage'      => 'Tahap',
            'created-at' => 'Dibuat Pada',
        ],

        'filters' => [
            'title'             => 'Judul',
            'priority'          => 'Prioritas',
            'low'               => 'Rendah',
            'high'              => 'Tinggi',
            'state'             => 'Status',
            'tags'              => 'Tag',
            'allocated-hours'   => 'Jam yang Dialokasikan',
            'total-hours-spent' => 'Total Jam Dihabiskan',
            'remaining-hours'   => 'Jam Tersisa',
            'overtime'          => 'Lembur',
            'progress'          => 'Progres',
            'deadline'          => 'Batas Waktu',
            'created-at'        => 'Dibuat Pada',
            'updated-at'        => 'Diperbarui Pada',
            'assignees'         => 'Penerima Tugas',
            'customer'          => 'Pelanggan',
            'project'           => 'Proyek',
            'stage'             => 'Tahap',
            'milestone'         => 'Tonggak',
            'company'           => 'Perusahaan',
            'creator'           => 'Pembuat',
        ],

        'actions' => [
            'update-state' => [
                'modal-heading' => 'Perbarui Status Tugas',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Tugas dipulihkan',
                    'body'  => 'Tugas berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tugas dihapus',
                    'body'  => 'Tugas berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tugas dihapus paksa',
                    'body'  => 'Tugas berhasil dihapus secara paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tugas dipulihkan',
                    'body'  => 'Tugas berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tugas dihapus',
                    'body'  => 'Tugas berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tugas dihapus paksa',
                    'body'  => 'Tugas berhasil dihapus secara paksa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'entries' => [
                    'title'       => 'Judul',
                    'state'       => 'Status',
                    'tags'        => 'Tag',
                    'priority'    => 'Prioritas',
                    'description' => 'Deskripsi',
                ],
            ],

            'project-information' => [
                'title' => 'Informasi Proyek',

                'entries' => [
                    'project'   => 'Proyek',
                    'milestone' => 'Tonggak',
                    'customer'  => 'Pelanggan',
                    'assignees' => 'Penerima Tugas',
                    'deadline'  => 'Batas Waktu',
                    'stage'     => 'Tahap',
                ],
            ],

            'time-tracking' => [
                'title' => 'Pelacakan Waktu',

                'entries' => [
                    'allocated-time'        => 'Waktu Dialokasikan',
                    'time-spent'            => 'Waktu Terpakai',
                    'time-spent-suffix'     => ' Jam',
                    'time-remaining'        => 'Sisa Waktu',
                    'time-remaining-suffix' => ' Jam',
                    'progress'              => 'Progres',
                ],
            ],

            'additional-information' => [
                'title' => 'Informasi Tambahan',
            ],

            'record-information' => [
                'title' => 'Informasi Rekaman',

                'entries' => [
                    'created-at'   => 'Dibuat Pada',
                    'created-by'   => 'Dibuat Oleh',
                    'last-updated' => 'Terakhir Diperbarui',
                ],
            ],

            'statistics' => [
                'title' => 'Statistik',

                'entries' => [
                    'sub-tasks'         => 'Subtugas',
                    'timesheet-entries' => 'Entri Lembar Waktu',
                ],
            ],
        ],
    ],
];
