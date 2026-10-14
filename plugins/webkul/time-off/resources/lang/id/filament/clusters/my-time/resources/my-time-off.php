<?php

return [
    'title' => 'Cuti',

    'model-label' => 'Cuti Saya',

    'navigation' => [
        'title' => 'Cuti Saya',
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Tipe cuti',
            'date'              => 'Tanggal',
            'dates'             => 'Tanggal',
            'request-date-from' => 'Tanggal Permintaan Dari',
            'request-date-to'   => 'Tanggal Permintaan Hingga',
            'description'       => 'Deskripsi',
            'period'            => 'Periode',
            'half-day'          => 'Setengah Hari',
            'requested-days'    => 'Diminta (Hari/Jam)',
            'description'       => 'Deskripsi',
            'attachment'        => 'Lampiran',
            'day'               => ':day hari',
            'days'              => ':days hari',
        ],
    ],

    'table' => [
        'columns' => [
            'employee-name'  => 'Karyawan',
            'time-off-type'  => 'Tipe Cuti',
            'description'    => 'Deskripsi',
            'date-from'      => 'Tanggal Dari',
            'date-to'        => 'Tanggal Hingga',
            'duration'       => 'Durasi',
            'status'         => 'Status',
        ],

        'groups' => [
            'employee-name' => 'Karyawan',
            'time-off-type' => 'Tipe Cuti',
            'status'        => 'Status',
            'start-date'    => 'Tanggal Mulai',
            'start-to'      => 'Tanggal Akhir',
            'updated-at'    => 'Diperbarui Pada',
            'created-at'    => 'Dibuat Pada',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validasi',
                    'approve'  => 'Setujui',
                ],
                'notification' => [
                    'title' => 'Cuti disetujui',
                    'body'  => 'Cuti berhasil disetujui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Cuti dihapus',
                    'body'  => 'Cuti berhasil dihapus.',
                ],
            ],

            'refused' => [
                'title'        => 'Tolak',
                'notification' => [
                    'title' => 'Cuti ditolak',
                    'body'  => 'Cuti berhasil ditolak.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Cuti dihapus',
                    'body'  => 'Cuti berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'     => 'Tipe cuti',
            'date'              => 'Tanggal',
            'dates'             => 'Tanggal',
            'request-date-from' => 'Tanggal Permintaan Dari',
            'request-date-to'   => 'Tanggal Permintaan Hingga',
            'description'       => 'Deskripsi',
            'period'            => 'Periode',
            'half-day'          => 'Setengah Hari',
            'requested-days'    => 'Diminta (Hari/Jam)',
            'description'       => 'Deskripsi',
            'attachment'        => 'Lampiran',
            'day'               => ':day hari',
            'days'              => ':days hari',
        ],
    ],
];
