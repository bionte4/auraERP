<?php

return [
    'form' => [
        'name'          => 'Nama',
        'level'         => 'Tingkat',
        'default-level' => 'Tingkat Bawaan',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Nama',
            'level'         => 'Tingkat',
            'default-level' => 'Tingkat Bawaan',
            'created-at'    => 'Dibuat Pada',
            'updated-at'    => 'Diperbarui Pada',
        ],

        'groups' => [
            'created-at' => 'Dibuat Pada',
        ],

        'filters' => [
            'deleted-records' => 'Data Terhapus',
        ],

        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tingkat Keterampilan dibuat',
                    'body'  => 'Tingkat keahlian berhasil dibuat.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'Tingkat Keterampilan diperbarui',
                    'body'  => 'Tingkat keahlian berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Tingkat Keterampilan dipulihkan',
                    'body'  => 'Tingkat keahlian berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tingkat Keterampilan dihapus',
                    'body'  => 'Tingkat keahlian berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tingkat Keterampilan dihapus',
                    'body'  => 'Keahlian berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tingkat Keterampilan dihapus permanen',
                    'body'  => 'Keahlian berhasil dihapus secara paksa.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Tingkat Keterampilan dipulihkan',
                    'body'  => 'Keahlian berhasil dipulihkan secara paksa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'          => 'Nama',
            'level'         => 'Tingkat',
            'default-level' => 'Tingkat Bawaan',
        ],
    ],
];
