<?php

return [
    'navigation' => [
        'title' => 'Tag',
    ],

    'form' => [
        'name'  => 'Nama',
        'color' => 'Warna',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'color'      => 'Warna',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tag diperbarui',
                    'body'  => 'Tag berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Tag dipulihkan',
                    'body'  => 'Tag berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag dihapus',
                    'body'  => 'Tag berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tag dihapus paksa',
                    'body'  => 'Tag berhasil dihapus secara paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tag dipulihkan',
                    'body'  => 'Tag berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag dihapus',
                    'body'  => 'Tag berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tag dihapus paksa',
                    'body'  => 'Tag berhasil dihapus secara paksa.',
                ],
            ],
        ],
    ],
];
