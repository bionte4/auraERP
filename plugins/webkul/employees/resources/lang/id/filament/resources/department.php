<?php

return [
    'title' => 'Departemen',

    'navigation' => [
        'title' => 'Departemen',
    ],

    'global-search' => [
        'department-manager' => 'Manajer',
        'company'            => 'Perusahaan',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'fields' => [
                    'name'                => 'Nama',
                    'manager'             => 'Manajer',
                    'parent-department'   => 'Departemen Induk',
                    'manager-placeholder' => 'Pilih Manajer',
                    'company'             => 'Perusahaan',
                    'company-placeholder' => 'Pilih Perusahaan',
                    'color'               => 'Warna',
                ],
            ],

            'additional' => [
                'title'       => 'Informasi Tambahan',
                'description' => 'Informasi tambahan tentang departemen ini.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nama',
            'manager-name' => 'Manajer',
            'company-name' => 'Perusahaan',
        ],

        'groups' => [
            'name'       => 'Nama',
            'manager'    => 'Manajer',
            'company'    => 'Perusahaan',
            'updated-at' => 'Diperbarui Pada',
            'created-at' => 'Dibuat Pada',
        ],

        'filters' => [
            'name'         => 'Nama',
            'manager-name' => 'Manajer',
            'company-name' => 'Perusahaan',
            'updated-at'   => 'Diperbarui Pada',
            'created-at'   => 'Dibuat Pada',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Departemen dipulihkan',
                    'body'  => 'Departemen berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departemen dihapus',
                    'body'  => 'Departemen berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Departemen dihapus permanen',
                    'body'  => 'Departemen berhasil dihapus paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Departemen dipulihkan',
                    'body'  => 'Departemen berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Departemen dihapus',
                    'body'  => 'Departemen berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Departemen dihapus permanen',
                    'body'  => 'Departemen berhasil dihapus paksa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'entries' => [
                    'name'            => 'Nama',
                    'manager'         => 'Manajer',
                    'company'         => 'Perusahaan',
                    'color'           => 'Warna',
                    'hierarchy-title' => 'Organisasi Departemen',
                ],
            ],
        ],
    ],
];
