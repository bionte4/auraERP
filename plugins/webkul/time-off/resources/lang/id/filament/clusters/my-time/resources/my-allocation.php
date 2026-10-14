<?php

return [
    'title' => 'Alokasi Saya',

    'model-label' => 'Alokasi Saya',

    'navigation' => [
        'title' => 'Alokasi Saya',
    ],

    'form' => [
        'fields' => [
            'name'                => 'Nama',
            'name-placeholder'    => 'Jenis Cuti (Dari awal masa berlaku hingga akhir masa berlaku/tanpa batas)',
            'time-off-type'       => 'Tipe Cuti',
            'allocation-type'     => 'Jenis Alokasi',
            'validity-period'     => 'Masa Berlaku',
            'date-from'           => 'Tanggal Dari',
            'date-to'             => 'Tanggal Hingga',
            'date-to-placeholder' => 'Tanpa Batas',
            'allocation'          => 'Alokasi',
            'allocation-suffix'   => 'Jumlah Hari',
            'reason'              => 'Alasan',
        ],
    ],

    'table' => [
        'columns' => [
            'time-off-type'   => 'Tipe Cuti',
            'amount'          => 'Jumlah',
            'allocation-type' => 'Jenis Alokasi',
            'status'          => 'Status',
        ],

        'groups' => [
            'time-off-type'   => 'Tipe Cuti',
            'employee-name'   => 'Nama Karyawan',
            'allocation-type' => 'Jenis Alokasi',
            'status'          => 'Status',
            'start-date'      => 'Tanggal Mulai',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validasi',
                    'approve'  => 'Setujui',
                ],
                'notification' => [
                    'title' => 'Alokasi disetujui',
                    'body'  => 'Alokasi yang disetujui berhasil disetujui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Alokasi dihapus',
                    'body'  => 'Alokasi berhasil dihapus.',
                ],
            ],

            'refused' => [
                'title'        => 'Tolak',
                'notification' => [
                    'title' => 'Alokasi ditolak',
                    'body'  => 'Alokasi berhasil ditolak.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Alokasi dihapus',
                    'body'  => 'Alokasi berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title'   => 'Detail Alokasi',
                'entries' => [
                    'name'                => 'Nama',
                    'time-off-type'       => 'Tipe Cuti',
                    'allocation-type'     => 'Jenis Alokasi',
                ],
            ],

            'validity-period' => [
                'title'   => 'Masa Berlaku',
                'entries' => [
                    'date-from' => 'Tanggal Dari',
                    'date-to'   => 'Tanggal Hingga',
                    'reason'    => 'Alasan',
                ],
            ],
            'allocation-status' => [
                'title'   => 'Status Alokasi',
                'entries' => [
                    'date-to-placeholder' => 'Tanpa Batas',
                    'allocation'          => 'Jumlah Hari',
                    'allocation-value'    => ':days jumlah hari',
                    'state'               => 'Status',
                ],
            ],
        ],
    ],
];
