<?php

return [
    'form' => [
        'fields' => [
            'code' => 'Kode',
            'name' => 'Nama',
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Kode',
            'name'       => 'Nama',
            'created-by' => 'Dibuat Oleh',
        ],

        'groups' => [
            'code' => 'Kode',
            'name' => 'Nama',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Incoterm diperbarui',
                    'body'  => 'Incoterm berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm dihapus',
                    'body'  => 'Incoterm berhasil dihapus.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Incoterm dipulihkan',
                    'body'  => 'Incoterm berhasil dipulihkan.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Incoterms dipulihkan',
                    'body'  => 'Incoterm berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm dihapus',
                    'body'  => 'Incoterm berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterms dihapus permanen',
                    'body'  => 'Incoterms berhasil dihapus paksa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nama',
            'code' => 'Kode',
        ],
    ],
];
