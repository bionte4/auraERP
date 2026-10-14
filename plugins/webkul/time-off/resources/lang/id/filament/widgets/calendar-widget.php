<?php

return [
    'heading' => [
        'title' => 'Permintaan Cuti',
    ],

    'modal-actions' => [
        'edit' => [
            'title'                         => 'Ubah',
            'duration-display'              => ':count hari kerja|:count hari kerja',
            'duration-display-with-weekend' => ':count hari kerja (+ :weekend hari akhir pekan)|:count hari kerja (+ :weekend hari akhir pekan)',

            'notification' => [
                'title' => 'Cuti Diperbarui',
                'body'  => 'Permintaan cuti Anda berhasil diperbarui.',
            ],
        ],

        'delete' => [
            'title' => 'Hapus',
        ],
    ],

    'config' => [
        'button-text' => [
            'today' => 'Hari Ini',
            'month' => 'Bulan',
            'week'  => 'Minggu',
            'list'  => 'Daftar',
        ],
    ],

    'view-action' => [
        'title'       => 'Tampilan',
        'description' => 'Lihat Permintaan Cuti',
    ],

    'notifications' => [
        'employee-not-found' => [
            'title' => 'Karyawan Tidak Ditemukan',
            'body'  => 'Silakan tambahkan karyawan ke profil Anda sebelum mengajukan cuti.',
        ],

        'error' => [
            'title' => 'Terjadi kesalahan',
            'body'  => 'Kami tidak dapat memproses permintaan cuti Anda. Silakan coba lagi.',
        ],
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

            'success' => [
                'notification' => [
                    'title' => 'Cuti Dibuat',
                    'body'  => 'Permintaan cuti Anda berhasil dibuat.',
                ],
            ],
        ],
    ],

    'form' => [
        'title'       => 'Permintaan Cuti',
        'description' => 'Buat atau ubah permintaan cuti Anda dengan detail berikut:',

        'fields' => [
            'time-off-type'             => 'Tipe Cuti',
            'time-off-type-placeholder' => 'Pilih jenis cuti',
            'time-off-type-helper'      => 'Pilih jenis cuti yang Anda ajukan.',
            'request-date-from'         => 'Tanggal Permintaan Dari',
            'request-date-to'           => 'Tanggal Permintaan Hingga',
            'period'                    => 'Periode',
            'half-day'                  => 'Setengah Hari',
            'half-day-helper'           => 'Alihkan untuk cuti setengah hari.',
            'requested-days'            => 'Diminta (Hari/Jam)',
            'description'               => 'Deskripsi',
            'description-placeholder'   => 'Tidak ada deskripsi',
            'description-helper'        => 'Berikan deskripsi singkat tentang permintaan cuti Anda.',
            'duration'                  => 'Durasi',
            'please-select-dates'       => 'Silakan pilih tanggal permintaan dari dan hingga.',
        ],
    ],

    'infolist' => [
        'title'       => 'Rincian Cuti',
        'description' => 'Berikut detail permintaan cuti Anda:',
        'entries'     => [
            'time-off-type'           => 'Tipe Cuti',
            'request-date-from'       => 'Tanggal Permintaan Dari',
            'request-date-to'         => 'Tanggal Permintaan Hingga',
            'description'             => 'Deskripsi',
            'description-placeholder' => 'Tidak ada deskripsi',
            'duration'                => 'Durasi',
            'status'                  => 'Status',
        ],
    ],

    'events' => [
        'title' => ':name Pada :status: :days hari',
    ],
];
