<?php

return [
    'modal' => [
        'title' => 'Jam Kerja',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Informasi Umum',
                'fields' => [
                    'attendance-name' => 'Nama Kehadiran',
                    'day-of-week'     => 'Hari dalam Pekan',
                ],
            ],

            'timing-information' => [
                'title' => 'Informasi Waktu',

                'fields' => [
                    'day-period' => 'Periode Hari',
                    'week-type'  => 'Jenis Pekan',
                    'work-from'  => 'Bekerja Dari',
                    'work-to'    => 'Kerja Hingga',
                ],
            ],

            'date-information' => [
                'title' => 'Informasi Tanggal',

                'fields' => [
                    'starting-date' => 'Tanggal Mulai',
                    'ending-date'   => 'Tanggal Berakhir',
                ],
            ],

            'additional-information' => [
                'title' => 'Informasi Tambahan',

                'fields' => [
                    'durations-days' => 'Durasi (Hari)',
                    'display-type'   => 'Tipe Tampilan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'          => 'Nama Kehadiran',
            'day-of-week'   => 'Hari dalam Pekan',
            'day-period'    => 'Periode Hari',
            'work-from'     => 'Bekerja Dari',
            'work-to'       => 'Kerja Hingga',
            'starting-date' => 'Tanggal Mulai',
            'ending-date'   => 'Tanggal Berakhir',
            'display-type'  => 'Tipe Tampilan',
            'created-by'    => 'Dibuat Oleh',
            'created-at'    => 'Dibuat Pada',
            'updated-at'    => 'Diperbarui Pada',
        ],

        'groups' => [
            'activity-type' => 'Tipe Aktivitas',
            'assignment'    => 'Penugasan',
            'assigned-to'   => 'Ditugaskan Kepada',
            'interval'      => 'Interval',
            'delay-unit'    => 'Satuan Penundaan',
            'delay-from'    => 'Penundaan Dari',
            'created-by'    => 'Dibuat Oleh',
            'created-at'    => 'Dibuat Pada',
            'updated-at'    => 'Diperbarui Pada',
        ],

        'filters' => [
            'display-type' => 'Tipe Tampilan',
            'day-of-week'  => 'Hari dalam Pekan',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Jam Kerja diperbarui',
                    'body'  => 'Jam kerja berhasil diperbarui.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Jam Kerja dibuat',
                    'body'  => 'Jam kerja berhasil dibuat.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jam Kerja dihapus',
                    'body'  => 'Jam kerja berhasil dihapus.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Jam Kerja dipulihkan',
                    'body'  => 'Jam kerja berhasil dipulihkan.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Jam Kerja dihapus',
                    'body'  => 'Jam kerja berhasil dihapus.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Jam Kerja dihapus',
                    'body'  => 'Jam kerja berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Jam Kerja dihapus',
                    'body'  => 'Jam kerja berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'name'        => 'Nama Kehadiran',
                    'day-of-week' => 'Hari dalam Pekan',
                ],
            ],

            'timing-information' => [
                'title' => 'Informasi Waktu',

                'entries' => [
                    'day-period' => 'Periode Hari',
                    'week-type'  => 'Jenis Pekan',
                    'work-from'  => 'Bekerja Dari',
                    'work-to'    => 'Kerja Hingga',
                ],
            ],

            'date-information' => [
                'title' => 'Informasi Tanggal',

                'entries' => [
                    'starting-date' => 'Tanggal Mulai',
                    'ending-date'   => 'Tanggal Berakhir',
                ],
            ],

            'additional-information' => [
                'title' => 'Informasi Tambahan',

                'entries' => [
                    'durations-days' => 'Durasi (Hari)',
                    'display-type'   => 'Tipe Tampilan',
                ],
            ],
        ],

        'note' => 'Catatan',
    ],
];
