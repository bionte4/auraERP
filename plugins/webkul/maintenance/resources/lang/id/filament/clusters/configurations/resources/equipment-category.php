<?php

return [
    'navigation' => [
        'title' => 'Kategori',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'fields' => [
                    'name'       => 'Nama',
                    'technician' => 'Penanggung Jawab',
                    'company'    => 'Perusahaan',
                    'note'       => 'Catatan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'technician' => 'Penanggung Jawab',
            'company'    => 'Perusahaan',
            'created-at' => 'Dibuat Pada',
        ],

        'groups' => [
            'technician' => 'Penanggung Jawab',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kategori diperbarui',
                    'body'  => 'Kategori berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kategori dihapus',
                    'body'  => 'Kategori berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kategori dihapus',
                    'body'  => 'Kategori berhasil dihapus.',
                ],
            ],
        ],

        'empty-state' => [
            'create' => [
                'notification' => [
                    'title' => 'Kategori dibuat',
                    'body'  => 'Kategori berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'name'       => 'Nama',
                    'technician' => 'Penanggung Jawab',
                    'company'    => 'Perusahaan',
                    'note'       => 'Catatan',
                ],
            ],
        ],
    ],
];
