<?php

return [
    'form' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Rincian Aktivitas',

                'fields' => [
                    'activity-type' => 'Tipe Aktivitas',
                    'summary'       => 'Ringkasan',
                    'note'          => 'Catatan',
                ],
            ],

            'assignment' => [
                'title' => 'Penugasan',

                'fields' => [
                    'assignment' => 'Penugasan',
                    'assignee'   => 'Penerima Tugas',
                ],
            ],

            'delay-information' => [
                'title' => 'Informasi Keterlambatan',

                'fields' => [
                    'delay-count'            => 'Jumlah Penundaan',
                    'delay-unit'             => 'Satuan Penundaan',
                    'delay-from'             => 'Penundaan Dari',
                    'delay-from-helper-text' => 'Sumber perhitungan keterlambatan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'activity-type' => 'Tipe Aktivitas',
            'summary'       => 'Ringkasan',
            'assignment'    => 'Penugasan',
            'assigned-to'   => 'Ditugaskan Kepada',
            'interval'      => 'Interval',
            'delay-unit'    => 'Satuan Penundaan',
            'delay-from'    => 'Penundaan Dari',
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
            'activity-type'   => 'Tipe Aktivitas',
            'activity-status' => 'Status Aktivitas',
            'has-delay'       => 'Ada Keterlambatan',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Templat aktivitas diperbarui',
                    'body'  => 'Templat aktivitas berhasil diperbarui.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Templat aktivitas dibuat',
                    'body'  => 'Templat aktivitas berhasil dibuat.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Templat aktivitas dihapus',
                    'body'  => 'Templat aktivitas berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Templat aktivitas dihapus',
                    'body'  => 'Templat aktivitas berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-details' => [
                'title' => 'Rincian Aktivitas',

                'entries' => [
                    'activity-type' => 'Tipe Aktivitas',
                    'summary'       => 'Ringkasan',
                ],
            ],

            'assignment' => [
                'title' => 'Penugasan',

                'entries' => [
                    'assignment' => 'Penugasan',
                    'assignee'   => 'Penerima Tugas',
                ],
            ],

            'delay-information' => [
                'title' => 'Informasi Keterlambatan',

                'entries' => [
                    'delay-count'            => 'Jumlah Penundaan',
                    'delay-unit'             => 'Satuan Penundaan',
                    'delay-from'             => 'Penundaan Dari',
                    'delay-from-helper-text' => 'Sumber perhitungan keterlambatan',
                ],
            ],
        ],

        'note' => 'Catatan',
    ],
];
