<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'skill-type'  => 'Jenis Keahlian',
                'skill'       => 'Keahlian',
                'skill-level' => 'Tingkat Keahlian',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'skill-type'    => 'Jenis Keahlian',
            'skill'         => 'Keahlian',
            'skill-level'   => 'Tingkat Keahlian',
            'level-percent' => 'Persen Tingkat',
            'created-by'    => 'Dibuat Oleh',
            'user'          => 'Pengguna',
            'created-at'    => 'Dibuat Pada',
        ],

        'groups' => [
            'skill-type' => 'Jenis Keahlian',
        ],

        'header-actions' => [
            'add-skill' => 'Tambah Keahlian',
        ],

        'filters' => [
            'activity-type'   => 'Tipe Aktivitas',
            'activity-status' => 'Status Aktivitas',
            'has-delay'       => 'Ada Keterlambatan',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Keterampilan diperbarui',
                    'body'  => 'Keahlian berhasil diperbarui.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Keterampilan dibuat',
                    'body'  => 'Keahlian berhasil dibuat.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Keterampilan dihapus',
                    'body'  => 'Keahlian berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Keterampilan dihapus',
                    'body'  => 'Keahlian berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'skill-type'    => 'Jenis Keahlian',
            'skill'         => 'Keahlian',
            'skill-level'   => 'Tingkat Keahlian',
            'level-percent' => 'Persen Tingkat',
        ],
    ],
];
