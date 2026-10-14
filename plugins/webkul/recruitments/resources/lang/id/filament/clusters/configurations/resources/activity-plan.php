<?php

return [
    'navigation' => [
        'title' => 'Rencana Aktivitas',
        'group' => 'Aktivitas',
    ],

    'global-search' => [
        'name'         => 'Departemen',
        'department'   => 'Departemen',
        'manager'      => 'Manajer',
        'company'      => 'Perusahaan',
        'plugin'       => 'Plugin',
        'creator-name' => 'Dibuat Oleh',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'status'     => 'Status',
            'department' => 'Departemen',
            'company'    => 'Perusahaan',
            'manager'    => 'Manajer',
            'created-by' => 'Dibuat Oleh',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'name'           => 'Nama',
            'plugin'         => 'Plugin',
            'activity-types' => 'Tipe Aktivitas',
            'company'        => 'Perusahaan',
            'department'     => 'Departemen',
            'is-active'      => 'Status',
            'updated-at'     => 'Diperbarui Pada',
            'created-at'     => 'Dibuat Pada',
        ],

        'groups' => [
            'status'     => 'Status',
            'name'       => 'Nama',
            'created-by' => 'Dibuat Oleh',
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
];
