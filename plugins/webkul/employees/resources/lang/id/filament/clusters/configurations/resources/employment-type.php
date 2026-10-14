<?php

return [
    'title' => 'Jenis Kepegawaian',

    'navigation' => [
        'title' => 'Jenis Kepegawaian',
        'group' => 'Rekrutmen',
    ],

    'form' => [
        'fields' => [
            'name'    => 'Jenis Kepegawaian',
            'code'    => 'Kode',
            'country' => 'Negara',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Jenis Kepegawaian',
            'code'       => 'Kode',
            'country'    => 'Negara',
            'created-by' => 'Dibuat Oleh',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'name'       => 'Jenis Kepegawaian',
            'country'    => 'Negara',
            'created-by' => 'Dibuat Oleh',
            'updated-at' => 'Diperbarui Pada',
            'created-at' => 'Dibuat Pada',
        ],

        'groups' => [
            'name'       => 'Jenis Kepegawaian',
            'country'    => 'Negara',
            'code'       => 'Kode',
            'created-by' => 'Dibuat Oleh',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Jenis Kepegawaian',
                    'body'  => 'Jenis kepegawaian berhasil diubah.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jenis Kepegawaian dihapus',
                    'body'  => 'Jenis kepegawaian berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Jenis Kepegawaian dihapus',
                    'body'  => 'Jenis kepegawaian berhasil dihapus.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Jenis Kepegawaian',
                    'body'  => 'Jenis kepegawaian berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Jenis Kepegawaian',
            'code'    => 'Kode',
            'country' => 'Negara',
        ],
    ],
];
