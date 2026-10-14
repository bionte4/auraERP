<?php

return [
    'form' => [
        'name'       => 'Nama',
        'short-name' => 'Nama Singkat',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'short-name' => 'Nama Singkat',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'creator' => 'Pembuat',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sapaan diperbarui',
                    'body'  => 'Gelar berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sapaan dihapus',
                    'body'  => 'Gelar berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Sapaan dihapus',
                    'body'  => 'Gelar berhasil dihapus.',
                ],
            ],
        ],
    ],
];
