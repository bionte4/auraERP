<?php

return [
    'navigation' => [
        'title' => 'Tahap',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nama',
            'done' => 'Selesai',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'done'       => 'Selesai',
            'created-at' => 'Dibuat Pada',
        ],

        'groups' => [
            'done'       => 'Selesai',
            'created-at' => 'Dibuat Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tahap diperbarui',
                    'body'  => 'Tahap berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tahap dihapus',
                    'body'  => 'Tahap berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tahapan dihapus',
                    'body'  => 'Tahap berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'name' => 'Nama',
                    'done' => 'Selesai',
                ],
            ],
        ],
    ],
];
