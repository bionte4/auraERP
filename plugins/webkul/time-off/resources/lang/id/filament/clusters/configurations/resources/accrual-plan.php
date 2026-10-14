<?php

return [
    'title'      => 'Rencana Akrual',
    'navigation' => [
        'title' => 'Rencana Akrual',
    ],

    'form' => [
        'fields' => [
            'name'                    => 'Judul',
            'is-based-on-worked-time' => 'Berdasarkan Waktu Kerja',
            'accrued-gain-time'       => 'Waktu Perolehan Terakrual',
            'carry-over-time'         => 'Waktu Pengalihan',
            'carry-over-date'         => 'Tanggal Pengalihan',
            'status'                  => 'Status',
        ],
    ],

    'table' => [
        'columns' => [
            'name'   => 'Nama',
            'levels' => 'Tingkat',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Rencana Akrual dihapus',
                    'body'  => 'Rencana akrual berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Rencana Akrual dihapus',
                    'body'  => 'Rencana akrual berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => 'Informasi Dasar',
        ],

        'entries' => [
            'name'                    => 'Nama',
            'is-based-on-worked-time' => 'Berdasarkan Waktu Kerja',
            'accrued-gain-time'       => 'Waktu Perolehan Terakrual',
            'carry-over-time'         => 'Waktu Pengalihan',
            'carry-over-day'          => 'Hari Pengalihan',
            'carry-over-month'        => 'Bulan Pengalihan',
        ],
    ],
];
