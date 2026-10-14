<?php

return [
    'navigation' => [
        'title' => 'Tahap Tugas',
    ],

    'form' => [
        'name'    => 'Nama',
        'project' => 'Proyek',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'project'    => 'Proyek',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'groups' => [
            'project'    => 'Proyek',
            'created-at' => 'Dibuat Pada',
        ],

        'filters' => [
            'project' => 'Proyek',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tahap tugas diperbarui',
                    'body'  => 'Tahap tugas berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Tahap tugas dipulihkan',
                    'body'  => 'Tahap tugas berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tahap tugas dihapus',
                    'body'  => 'Tahap tugas berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tahap tugas dihapus permanen',
                        'body'  => 'Tahap tugas berhasil dihapus paksa.',
                    ],
                    'error' => [
                        'title' => 'Tahap tugas tidak dapat dihapus',
                        'body'  => 'Tahap tugas tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tahap tugas dipulihkan',
                    'body'  => 'Tahap tugas berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tahap tugas dihapus',
                    'body'  => 'Tahap tugas berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tahap tugas dihapus permanen',
                    'body'  => 'Tahap tugas berhasil dihapus paksa.',
                ],
            ],
        ],
    ],
];
