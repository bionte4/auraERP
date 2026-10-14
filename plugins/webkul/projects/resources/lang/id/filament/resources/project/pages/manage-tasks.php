<?php

return [
    'title' => 'Tugas',

    'header-actions' => [
        'create' => [
            'label' => 'Tugas Baru',
        ],
    ],

    'table' => [
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

    'tabs' => [
        'open-tasks'       => 'Tugas Terbuka',
        'my-tasks'         => 'Tugas Saya',
        'unassigned-tasks' => 'Tugas yang Belum Ditugaskan',
        'closed-tasks'     => 'Tugas Ditutup',
        'starred-tasks'    => 'Tugas Berbintang',
        'archived-tasks'   => 'Tugas Diarsipkan',
    ],
];
