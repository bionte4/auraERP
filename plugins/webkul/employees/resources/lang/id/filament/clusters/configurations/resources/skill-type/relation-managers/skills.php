<?php

return [
    'form' => [
        'name' => 'Nama',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'groups' => [
            'created-at' => 'Dibuat Pada',
        ],

        'filters' => [
            'deleted-records' => 'Data Terhapus',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Keterampilan diperbarui',
                    'body'  => 'Keahlian berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Keterampilan dipulihkan',
                    'body'  => 'Keahlian berhasil dipulihkan.',
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

            'force-delete' => [
                'notification' => [
                    'title' => 'Keterampilan dihapus paksa',
                    'body'  => 'Keahlian berhasil dihapus secara paksa.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Keterampilan dipulihkan paksa',
                    'body'  => 'Keahlian berhasil dipulihkan secara paksa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nama',
        ],
    ],
];
