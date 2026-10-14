<?php

return [
    'model'  => 'Rencana kinerja',
    'plural' => 'Rencana kinerja',

    'navigation' => [
        'title' => 'Rencana kinerja',
    ],

    'form' => [
        'section'        => 'Rencana',
        'title'          => 'Judul',
        'employee'       => 'Karyawan',
        'status'         => 'Status',
        'period-start'   => 'Awal periode',
        'period-end'     => 'Akhir periode',
        'summary'        => 'Ringkasan',
        'summary-helper' => 'Jelaskan hasil yang ingin dicapai rencana ini.',
        'score-helper'   => 'Rata-rata tertimbang dari progres sasaran. Jumlah bobot tidak harus 100.',
    ],

    'infolist' => [
        'section' => 'Rencana',
    ],

    'table' => [
        'columns' => [
            'title'    => 'Judul',
            'employee' => 'Karyawan',
            'period'   => 'Periode',
            'status'   => 'Status',
            'score'    => 'Skor',
        ],
        'filters' => [
            'status'   => 'Status',
            'employee' => 'Karyawan',
        ],
        'empty' => [
            'heading'     => 'Belum ada rencana kinerja',
            'description' => 'Buat rencana untuk seorang karyawan, lalu tambahkan sasaran berbobot.',
        ],
    ],

    'goals' => [
        'title' => 'Sasaran',
        'model' => 'Sasaran',
        'form'  => [
            'title'         => 'Sasaran',
            'metric'        => 'Ukuran',
            'target'        => 'Target',
            'weight'        => 'Bobot',
            'weight-helper' => 'Bobot relatif yang dipakai untuk menghitung skor rencana.',
            'progress'      => 'Progres',
            'status'        => 'Status',
            'notes'         => 'Catatan',
        ],
        'empty' => [
            'heading'     => 'Belum ada sasaran',
            'description' => 'Tambahkan target individu yang menyusun rencana ini.',
        ],
    ],
];
