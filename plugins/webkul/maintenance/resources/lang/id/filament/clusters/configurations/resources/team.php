<?php

return [
    'navigation' => [
        'title' => 'Tim',
    ],

    'form' => [
        'name'    => 'Nama',
        'company' => 'Perusahaan',
        'users'   => 'Anggota Tim',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'company'    => 'Perusahaan',
            'users'      => 'Anggota Tim',
            'created-at' => 'Dibuat Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tim diperbarui',
                    'body'  => 'Tim berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Tim dipulihkan',
                    'body'  => 'Tim berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tim dihapus',
                    'body'  => 'Tim berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tim dihapus paksa',
                        'body'  => 'Tim berhasil dihapus secara paksa.',
                    ],
                    'error' => [
                        'title' => 'Tim tidak dapat dihapus secara permanen',
                        'body'  => 'Tim sedang digunakan dan tidak dapat dihapus paksa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tim dipulihkan',
                    'body'  => 'Tim berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tim dihapus',
                    'body'  => 'Tim berhasil dihapus.',
                ],
            ],
        ],
    ],
];
