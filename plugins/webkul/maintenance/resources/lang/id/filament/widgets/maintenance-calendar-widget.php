<?php

return [
    'heading' => [
        'title' => 'Kalender Pemeliharaan',
    ],

    'config' => [
        'button-text' => [
            'today' => 'Hari Ini',
            'year'  => 'Tahun',
            'month' => 'Bulan',
            'week'  => 'Minggu',
            'list'  => 'Daftar',
        ],
    ],

    'header-actions' => [
        'create' => [
            'label'         => 'Permintaan Baru',
            'modal-heading' => 'Permintaan Pemeliharaan Baru',
            'notification'  => [
                'success' => [
                    'title' => 'Permintaan pemeliharaan dibuat',
                    'body'  => 'Permintaan pemeliharaan berhasil dibuat.',
                ],
                'error' => [
                    'title' => 'Permintaan pemeliharaan tidak dapat dibuat',
                    'body'  => 'Buat tahap pemeliharaan dan tim terlebih dahulu.',
                ],
            ],
        ],
    ],

    'view-action' => [
        'label' => 'Tampilan',
    ],

    'modal-actions' => [
        'edit' => [
            'label' => 'Ubah',
        ],
    ],

    'form' => [
        'fields' => [
            'subject'      => 'Subjek',
            'scheduled-at' => 'Dijadwalkan Pada',
        ],
    ],

    'infolist' => [
        'title'   => 'Permintaan Pemeliharaan',
        'entries' => [
            'subject'          => 'Subjek',
            'date'             => 'Tanggal',
            'time'             => 'Waktu',
            'technician'       => 'Teknisi',
            'priority'         => 'Prioritas',
            'maintenance-type' => 'Jenis Pemeliharaan',
            'stage'            => 'Tahap',
        ],
    ],
];
