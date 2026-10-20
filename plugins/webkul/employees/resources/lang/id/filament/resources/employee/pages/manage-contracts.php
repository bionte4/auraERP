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
