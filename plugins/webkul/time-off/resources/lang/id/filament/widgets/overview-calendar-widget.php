<?php

return [
    'heading' => [
        'title' => 'Ringkasan Cuti',
    ],

    'modal-actions' => [
        'edit' => [
            'title'        => 'Ubah',
            'notification' => [
                'title' => 'Cuti Diperbarui',
                'body'  => 'Permintaan cuti berhasil diperbarui.',
            ],
        ],

        'delete' => [
            'title' => 'Hapus',
        ],
    ],

    'view-action' => [
        'title'       => 'Tampilan',
        'description' => 'Lihat Permintaan Cuti',
    ],

    'header-actions' => [
        'create' => [
            'title'       => 'Cuti Baru',
            'description' => 'Buat Pengajuan Cuti',

            'notification' => [
                'title' => 'Cuti Dibuat',
                'body'  => 'Permintaan cuti berhasil dibuat.',
            ],

            'employee-not-found' => [
                'notification' => [
                    'title' => 'Karyawan Tidak Ditemukan',
                    'body'  => 'Silakan tambahkan karyawan ke profil Anda sebelum membuat permintaan cuti.',
                ],
            ],
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Tipe Cuti',
            'request-date-from' => 'Tanggal Permintaan Dari',
            'request-date-to'   => 'Tanggal Permintaan Hingga',
            'period'            => 'Periode',
            'half-day'          => 'Setengah Hari',
            'requested-days'    => 'Diminta (Hari/Jam)',
            'description'       => 'Deskripsi',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'           => 'Tipe Cuti',
            'request-date-from'       => 'Tanggal Permintaan Dari',
            'request-date-to'         => 'Tanggal Permintaan Hingga',
            'description'             => 'Deskripsi',
            'description-placeholder' => 'Tidak ada deskripsi',
            'duration'                => 'Durasi',
            'status'                  => 'Status',
        ],
    ],
];
