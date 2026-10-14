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

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Templat rencana aktivitas dibuat',
                    'body'  => 'Templat rencana aktivitas berhasil dibuat.',
                ],
            ],
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Templat aktivitas diperbarui',
                    'body'  => 'Templat aktivitas berhasil diperbarui.',
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
];
