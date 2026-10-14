<?php

return [
    'navigation' => 'Absen',
    'title'      => 'Absen',

    'actions' => [
        'clock-in'  => 'Absen masuk',
        'clock-out' => 'Absen pulang',
        'locating'  => 'Mencari lokasi...',
        'enroll'    => 'Daftarkan wajah',
        'reenroll'  => 'Daftarkan ulang wajah',
    ],

    'face' => [
        'loading'      => 'Menyiapkan kamera dan model wajah...',
        'scanning'     => 'Mencari wajah...',
        'ready'        => 'Wajah sudah terdaftar. Arahkan wajah ke kamera, lalu absen.',
        'needs-enroll' => 'Arahkan wajah ke kamera, lalu daftarkan sekali.',
        'desktop'      => 'Laptop tidak memakai kamera. Tekan absen, lalu izinkan lokasi.',
    ],

    'status' => [
        'none'     => 'Anda belum absen masuk hari ini.',
        'open'     => 'Masuk pukul :in (:status).',
        'complete' => 'Absensi hari ini sudah ditutup. Masuk :in, pulang :out.',
    ],

    'missing-employee' => [
        'title' => 'Data karyawan tidak ditemukan',
        'body'  => 'Akun ini belum terhubung ke karyawan, jadi absen dari ponsel belum dapat digunakan.',
    ],

    'location-note' => 'Ponsel akan meminta izin lokasi. Koordinat disimpan hanya saat Anda absen masuk atau pulang. Di ponsel, buka situs ini lewat HTTPS supaya browser dapat mengirim GPS.',

    'face-note' => 'Kamera depan dipakai hanya di ponsel, saat Anda menekan tombol, untuk mencocokkan wajah. Foto tidak disimpan. Buka situs ini lewat HTTPS. Pencocokan ini dapat tertipu oleh foto wajah, jadi tetap gunakan di tempat kerja yang diawasi.',

    'desktop-note' => 'Di laptop atau netbook, absen hanya memakai lokasi. Kamera dan pencocokan wajah dipakai di ponsel.',

    'sites-note' => 'Absen diterima bila lokasi Anda berada di dalam salah satu lokasi kerja: :sites.',

    'sites-empty' => 'Belum ada lokasi kerja. Semua titik lokasi masih diterima sampai admin menambahkan lokasi di menu Lokasi kerja.',

    'tracking' => [
        'active' => 'Jejak lokasi aktif. Biarkan halaman ini terbuka. Titik tercatat:',
        'note'   => 'Selama absen masih terbuka, ponsel mengirim lokasi kira-kira setiap 30 detik. Pengiriman berhenti saat Anda absen pulang, menutup halaman, atau mengunci layar.',
    ],

    'links' => [
        'check-in'  => 'Lokasi masuk: :place',
        'check-out' => 'Lokasi pulang: :place',
    ],

    'notifications' => [
        'clock-in' => [
            'title' => 'Absen masuk tercatat',
            'body'  => 'Kedatangan dan lokasi Anda hari ini sudah disimpan.',
        ],
        'clock-out' => [
            'title' => 'Absen pulang tercatat',
            'body'  => 'Kepulangan dan lokasi Anda hari ini sudah disimpan.',
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
            'body'  => 'Akun Anda belum terhubung ke karyawan.',
        ],
        'location-denied' => [
            'title' => 'Izin lokasi diperlukan',
            'body'  => 'Izinkan akses lokasi di browser, lalu coba lagi.',
        ],
        'location-unavailable' => [
            'title' => 'Perangkat ini tidak dapat mengirim lokasi',
            'body'  => 'Absen dari browser ponsel yang mendukung lokasi.',
        ],
        'location-invalid' => [
            'title' => 'Lokasi tidak terbaca',
            'body'  => 'Coba lagi di tempat dengan sinyal GPS yang lebih jelas.',
        ],
        'outside-site' => [
            'title' => 'Di luar lokasi kerja',
            'body'  => 'Anda berada di luar radius semua lokasi kerja yang aktif. Dekati lokasi proyek, lalu coba lagi.',
        ],
        'forbidden' => [
            'title' => 'Anda tidak dapat mencatat absensi ini',
            'body'  => 'Akun Anda tidak memiliki izin untuk tindakan ini.',
        ],
        'face-enrolled' => [
            'title' => 'Wajah terdaftar',
            'body'  => 'Absen masuk dan pulang berikutnya akan mencocokkan wajah ini.',
        ],
        'face-not-found' => [
            'title' => 'Wajah tidak terlihat',
            'body'  => 'Hadapkan wajah ke kamera dengan cahaya yang cukup, lalu coba lagi.',
        ],
        'face-missing' => [
            'title' => 'Wajah belum didaftarkan',
            'body'  => 'Daftarkan wajah Anda sekali sebelum absen.',
        ],
        'face-mismatch' => [
            'title' => 'Wajah tidak cocok',
            'body'  => 'Wajah di kamera tidak cocok dengan data yang terdaftar untuk karyawan ini.',
        ],
        'face-invalid' => [
            'title' => 'Data wajah tidak terbaca',
            'body'  => 'Muat ulang halaman, lalu coba daftarkan atau absen lagi.',
        ],
        'camera-denied' => [
            'title' => 'Kamera diperlukan',
            'body'  => 'Izinkan kamera di browser. Di ponsel, situs harus dibuka lewat HTTPS.',
        ],
    ],
];
