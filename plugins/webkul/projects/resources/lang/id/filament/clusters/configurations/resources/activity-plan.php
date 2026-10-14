<?php

return [
    'navigation' => [
        'title' => 'Rencana Aktivitas',
    ],

    'form' => [
        'name'   => 'Nama',
        'status' => 'Status',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'status'     => 'Status',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'groups' => [
            'name'       => 'Nama',
            'status'     => 'Status',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Rencana Aktivitas dipulihkan',
                    'body'  => 'Rencana aktivitas berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rencana Aktivitas dihapus',
                    'body'  => 'Rencana aktivitas berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Rencana Aktivitas dihapus permanen',
                    'body'  => 'Rencana aktivitas berhasil dihapus paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Rencana Aktivitas dipulihkan',
                    'body'  => 'Rencana aktivitas berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rencana Aktivitas dihapus',
                    'body'  => 'Rencana aktivitas berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Rencana Aktivitas dihapus permanen',
                    'body'  => 'Rencana aktivitas berhasil dihapus paksa.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Rencana Aktivitas dibuat',
                    'body'  => 'Rencana aktivitas berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'   => 'Nama',
        'status' => 'Status',
    ],
];
