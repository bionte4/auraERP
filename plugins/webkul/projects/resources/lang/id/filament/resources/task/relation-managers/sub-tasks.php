<?php

return [
    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Tambah Subtugas',

                'notification' => [
                    'title' => 'Tugas dibuat',
                    'body'  => 'Tugas berhasil dibuat.',
                ],
            ],
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tugas dipulihkan',
                    'body'  => 'Tugas berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tugas dihapus',
                    'body'  => 'Tugas berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tugas dihapus paksa',
                    'body'  => 'Tugas berhasil dihapus secara paksa.',
                ],
            ],
        ],
    ],
];
