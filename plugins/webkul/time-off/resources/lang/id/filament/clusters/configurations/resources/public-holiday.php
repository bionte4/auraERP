<?php

return [
    'title' => 'Hari Libur Nasional',

    'model-label' => 'Hari libur nasional',

    'navigation' => [
        'title' => 'Hari Libur Nasional',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nama',
            'name-placeholder' => 'Masukkan nama hari libur',
            'date-from'        => 'Tanggal Mulai',
            'date-to'          => 'Tanggal Akhir',
            'color'            => 'Warna',
            'calendar'         => 'Kalender',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nama',
            'company-name' => 'Nama Perusahaan',
            'calendar'     => 'Kalender',
            'created-by'   => 'Dibuat Oleh',
            'date-from'    => 'Tanggal Mulai',
            'date-to'      => 'Tanggal Akhir',
        ],

        'filters' => [
            'name'         => 'Nama',
            'company-name' => 'Nama Perusahaan',
            'created-by'   => 'Dibuat Oleh',
            'date-from'    => 'Tanggal Mulai',
            'date-to'      => 'Tanggal Akhir',
            'created-at'   => 'Dibuat Pada',
            'updated-at'   => 'Diperbarui Pada',
        ],

        'groups' => [
            'name'         => 'Nama',
            'company-name' => 'Nama Perusahaan',
            'created-by'   => 'Dibuat Oleh',
            'date-from'    => 'Tanggal Mulai',
            'date-to'      => 'Tanggal Akhir',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Hari libur nasional diperbarui',
                    'body'  => 'Hari libur nasional berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Hari libur nasional dihapus',
                    'body'  => 'Hari libur nasional berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Hari libur nasional dihapus',
                    'body'  => 'Hari libur nasional berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'      => 'Nama',
            'date-from' => 'Tanggal Mulai',
            'date-to'   => 'Tanggal Akhir',
            'color'     => 'Warna',
        ],
    ],
];
