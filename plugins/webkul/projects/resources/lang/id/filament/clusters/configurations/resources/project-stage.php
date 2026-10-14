<?php

return [
    'navigation' => [
        'title' => 'Tahap Proyek',
    ],

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
            'name'         => 'Nama',
            'is-completed' => 'Telah Selesai',
            'project'      => 'Proyek',
            'created-at'   => 'Dibuat Pada',
        ],

        'filters' => [
            'is-completed' => 'Telah Selesai',
            'project'      => 'Proyek',
            'creator'      => 'Pembuat',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tahap proyek diperbarui',
                    'body'  => 'Tahap proyek berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Tahap proyek dipulihkan',
                    'body'  => 'Tahap proyek berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tahap proyek dihapus',
                    'body'  => 'Tahap proyek berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tahap proyek dihapus permanen',
                        'body'  => 'Tahap proyek berhasil dihapus paksa.',
                    ],
                    'error' => [
                        'title' => 'Tahap proyek tidak dapat dihapus',
                        'body'  => 'Tahap proyek tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tahap proyek dipulihkan',
                    'body'  => 'Tahap proyek berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tahap proyek dihapus',
                    'body'  => 'Tahap proyek berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tahap proyek dihapus permanen',
                    'body'  => 'Tahap proyek berhasil dihapus paksa.',
                ],
            ],
        ],
    ],
];
