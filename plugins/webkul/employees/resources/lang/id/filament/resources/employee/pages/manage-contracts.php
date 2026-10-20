<?php

return [
    'navigation' => [
        'title' => 'Riwayat Kontrak',
    ],

    'fields' => [
        'employment-type' => 'Tipe Karyawan',
        'contract-number' => 'Nomor Kontrak',
        'start-date'      => 'Tanggal Mulai',
        'end-date'        => 'Tanggal Berakhir',
        'status'          => 'Status',
        'status-helper-text' => 'Aktif = kontrak berjalan (hanya boleh satu yang aktif; mengaktifkan kontrak yang lebih baru otomatis mengubah kontrak aktif sebelumnya menjadi Diperpanjang). Selesai = berakhir normal. Dialihkan ke Tetap = berubah menjadi PKWTT. Terminasi = dihentikan sebelum tanggal berakhir.',
        'notes'           => 'Catatan',
    ],

    'columns' => [
        'contract' => 'Kontrak',
        'duration' => 'Durasi',
        'months'   => 'bulan',
        'source'   => 'Sumber',
    ],

    'status' => [
        'active'     => 'Aktif',
        'completed'  => 'Selesai',
        'extended'   => 'Diperpanjang',
        'converted'  => 'Dialihkan ke Tetap (PKWTT)',
        'terminated' => 'Terminasi',
    ],

    'actions' => [
        'create' => 'Kontrak Baru',
        'extend' => 'Perpanjang Kontrak',
    ],
];
