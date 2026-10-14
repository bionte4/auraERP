<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'Perusahaan',
                'country'            => 'Negara',
                'name'               => 'Nama',
                'preceding-subtotal' => 'Subtotal Sebelumnya',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'Perusahaan',
            'country'            => 'Negara',
            'created-by'         => 'Dibuat Oleh',
            'name'               => 'Nama',
            'preceding-subtotal' => 'Subtotal Sebelumnya',
            'created-at'         => 'Dibuat Pada',
            'updated-at'         => 'Diperbarui Pada',
        ],

        'groups' => [
            'name'       => 'Nama',
            'company'    => 'Perusahaan',
            'country'    => 'Negara',
            'created-by' => 'Dibuat Oleh',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Grup Pajak dihapus',
                        'body'  => 'Grup pajak berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Grup Pajak tidak dapat dihapus',
                        'body'  => 'Grup pajak tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Grup Pajak dihapus',
                        'body'  => 'Grup pajak berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Grup Pajak tidak dapat dihapus',
                        'body'  => 'Grup pajak tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company'            => 'Perusahaan',
                'country'            => 'Negara',
                'name'               => 'Nama',
                'preceding-subtotal' => 'Subtotal Sebelumnya',
            ],
        ],
    ],
];
