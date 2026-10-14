<?php

return [
    'title' => 'Gelar',

    'navigation' => [
        'title' => 'Gelar',
        'group' => 'Lamaran',
    ],

    'groups' => [
        'status'     => 'Status',
        'created-by' => 'Dibuat Oleh',
        'created-at' => 'Dibuat Pada',
        'updated-at' => 'Diperbarui Pada',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nama',
            'name-placeholder' => 'Masukkan nama gelar',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nama',
            'created-by' => 'Dibuat Oleh',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'name'       => 'Nama',
            'employee'   => 'Karyawan',
            'created-by' => 'Dibuat Oleh',
            'updated-at' => 'Diperbarui Pada',
            'created-at' => 'Dibuat Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Gelar diperbarui',
                    'body'  => 'Jenjang pendidikan berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Gelar dihapus',
                    'body'  => 'Jenjang pendidikan berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Gelar dihapus',
                    'body'  => 'Jenjang pendidikan berhasil dihapus.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Gelar dibuat',
                    'body'  => 'Jenjang pendidikan berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nama',
    ],
];
