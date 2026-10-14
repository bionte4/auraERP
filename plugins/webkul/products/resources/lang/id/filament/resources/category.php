<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',

                'fields' => [
                    'name'             => 'Nama',
                    'name-placeholder' => 'mis. Lampu',
                    'parent'           => 'Induk',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nama',
            'full-name'   => 'Nama Lengkap',
            'parent-path' => 'Jalur Induk',
            'parent'      => 'Induk',
            'creator'     => 'Pembuat',
            'created-at'  => 'Dibuat Pada',
            'created-at'  => 'Dibuat Pada',
            'updated-at'  => 'Diperbarui Pada',
        ],

        'groups' => [
            'parent'     => 'Induk',
            'creator'    => 'Pembuat',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'parent'  => 'Induk',
            'creator' => 'Pembuat',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kategori dihapus',
                        'body'  => 'Kategori berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Kategori tidak dapat dihapus',
                        'body'  => 'Kategori tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kategori dihapus',
                        'body'  => 'Kategori berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Kategori tidak dapat dihapus',
                        'body'  => 'Kategori tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'name'        => 'Nama',
                    'parent'      => 'Kategori Induk',
                    'full_name'   => 'Nama Lengkap Kategori',
                    'parent_path' => 'Jalur Kategori',
                ],
            ],

            'record-information' => [
                'title' => 'Informasi Rekaman',

                'entries' => [
                    'creator'    => 'Dibuat Oleh',
                    'created_at' => 'Dibuat Pada',
                    'updated_at' => 'Terakhir Diperbarui Pada',
                ],
            ],
        ],
    ],
];
