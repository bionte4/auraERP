<?php

return [
    'header-actions' => [
        'create' => [
            'label' => 'Catat absensi',
        ],
        'clock-in' => [
            'label' => 'Absen masuk',
        ],
        'clock-out' => [
            'label' => 'Absen pulang',
        ],
    ],

    'tabs' => [
        'today' => 'Hari ini',
        'mine'  => 'Absensi saya',
    ],

    'notifications' => [
        'clock-in' => [
            'title' => 'Absen masuk tercatat',
            'body'  => 'Kedatangan Anda hari ini sudah disimpan.',
        ],
        'clock-out' => [
            'title' => 'Absen pulang tercatat',
            'body'  => 'Kepulangan Anda hari ini sudah disimpan.',
        ],
        'already-in' => [
            'title' => 'Sudah absen masuk',
            'body'  => 'Anda sudah memiliki absen masuk untuk hari ini.',
        ],
        'missing-open' => [
            'title' => 'Tidak ada absen masuk terbuka',
            'body'  => 'Absen masuk terlebih dahulu, atau absensi hari ini sudah ditutup.',
        ],
        'missing-employee' => [
            'title' => 'Data karyawan tidak ditemukan',
            'body'  => 'Akun Anda belum terhubung ke karyawan. Catat absensi lewat formulir, atau hubungkan pengguna ini pada data karyawan.',
        ],
    ],
];
