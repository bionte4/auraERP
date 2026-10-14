<?php

return [
    'title' => 'Alasan Penolakan',

    'navigation' => [
        'title' => 'Alasan Penolakan',
        'group' => 'Lamaran',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nama',
            'template'         => [
                'title'                    => 'Templat',
                'applicant-refuse'         => 'Penolakan Pelamar',
                'applicant-not-interested' => 'Pelamar Tidak Tertarik',
            ],
            'name-placeholder' => 'Masukkan nama alasan penolakan',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nama',
            'template'   => 'Templat',
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
                    'title' => 'Alasan penolakan diperbarui',
                    'body'  => 'Alasan penolakan berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Alasan penolakan dihapus',
                    'body'  => 'Alasan penolakan berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Alasan penolakan dihapus',
                    'body'  => 'Alasan penolakan berhasil dihapus.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Alasan penolakan dibuat',
                    'body'  => 'Alasan penolakan berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'       => 'Nama',
        'template'   => 'Templat',
    ],
];
