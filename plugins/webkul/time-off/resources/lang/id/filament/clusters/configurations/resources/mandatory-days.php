<?php

return [
    'title' => 'Hari Wajib',

    'model-label' => 'Hari Wajib',

    'navigation' => [
        'title' => 'Hari Libur Wajib',
    ],

    'form' => [
        'fields' => [
            'name'       => 'Nama',
            'start-date' => 'Tanggal Mulai',
            'end-date'   => 'Tanggal Akhir',
            'color'      => 'Warna',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nama',
            'company-name' => 'Nama Perusahaan',
            'created-by'   => 'Dibuat Oleh',
            'start-date'   => 'Tanggal Mulai',
            'end-date'     => 'Tanggal Akhir',
        ],

        'filters' => [
            'name'         => 'Nama',
            'company-name' => 'Nama Perusahaan',
            'created-by'   => 'Dibuat Oleh',
            'start-date'   => 'Tanggal Mulai',
            'end-date'     => 'Tanggal Akhir',
            'created-at'   => 'Dibuat Pada',
            'updated-at'   => 'Diperbarui Pada',
        ],

        'groups' => [
            'name'         => 'Nama',
            'company-name' => 'Nama Perusahaan',
            'created-by'   => 'Dibuat Oleh',
            'start-date'   => 'Tanggal Mulai',
            'end-date'     => 'Tanggal Akhir',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Hari wajib diperbarui',
                    'body'  => 'Hari wajib berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hari wajib dihapus',
                    'body'  => 'Hari wajib berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Hari wajib dihapus',
                    'body'  => 'Hari wajib berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'       => 'Nama',
            'start-date' => 'Tanggal Mulai',
            'end-date'   => 'Tanggal Akhir',
            'color'      => 'Warna',
        ],
    ],
];
