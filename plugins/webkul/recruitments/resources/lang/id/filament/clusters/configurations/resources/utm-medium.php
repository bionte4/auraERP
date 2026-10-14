<?php

return [
    'title' => 'Sedang',

    'navigation' => [
        'title' => 'Sedang',
        'group' => 'UTMs',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nama',
            'name-placeholder' => 'Masukkan nama media',
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
            'created-by' => 'Dibuat Oleh',
            'updated-at' => 'Diperbarui Pada',
            'created-at' => 'Dibuat Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Media diperbarui',
                    'body'  => 'Media berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Media dihapus',
                    'body'  => 'Media berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Media dihapus',
                    'body'  => 'Media berhasil dihapus.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Media dibuat',
                    'body'  => 'Media berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nama',
    ],
];
