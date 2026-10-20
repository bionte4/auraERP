<?php

return [
    'title' => 'Cuti',

    'model-label' => 'Cuti',

    'navigation' => [
        'title'         => 'Cuti',
        'badge-tooltip' => 'Pengajuan cuti menunggu persetujuan',
    ],

    'global-search' => [
        'employee'      => 'Karyawan',
        'time-off-type' => 'Tipe Cuti',
        'date-from'     => 'Tanggal Dari',
        'date-to'       => 'Tanggal Hingga',
    ],

    'form' => [
        'fields' => [
            'employee-name'     => 'Nama Karyawan',
            'department-name'   => 'Nama Departemen',
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
            'employee-name'     => 'Nama Karyawan',
            'department-name'   => 'Nama Departemen',
            'time-off-type'     => 'Tipe cuti',
            'date'              => 'Tanggal',
            'dates'             => 'Tanggal',
            'request-date-from' => 'Tanggal Permintaan Dari',
            'request-date-to'   => 'Tanggal Permintaan Hingga',
            'description'       => 'Deskripsi',
            'period'            => 'Periode',
            'half-day'          => 'Setengah Hari',
            'requested-days'    => 'Diminta (Hari/Jam)',
            'attachment'        => 'Lampiran',
            'day'               => ':day hari',
            'days'              => ':days hari',
            'date-from'         => 'Tanggal Dari',
            'date-to'           => 'Tanggal Hingga',
            'status'            => 'Status',
        ],
    ],
];
