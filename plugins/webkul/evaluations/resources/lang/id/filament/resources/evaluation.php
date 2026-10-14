<?php

return [
    'model'  => 'Evaluasi kinerja',
    'plural' => 'Evaluasi kinerja',

    'navigation' => [
        'title' => 'Evaluasi kinerja',
    ],

    'form' => [
        'section'           => 'Evaluasi',
        'title'             => 'Judul',
        'employee'          => 'Karyawan',
        'reviewer'          => 'Penilai',
        'status'            => 'Status',
        'period-start'      => 'Awal periode',
        'period-end'        => 'Akhir periode',
        'summary'           => 'Catatan penilai',
        'summary-helper'    => 'Ringkasan penilaian untuk periode ini.',
        'employee-comment'  => 'Tanggapan karyawan',
        'score-helper'      => 'Rata-rata tertimbang dari kriteria berskala 1 sampai 5.',
    ],

    'infolist' => [
        'section' => 'Evaluasi',
    ],

    'table' => [
        'columns' => [
            'title'    => 'Judul',
            'employee' => 'Karyawan',
            'reviewer' => 'Penilai',
            'period'   => 'Periode',
            'status'   => 'Status',
            'score'    => 'Nilai',
        ],
        'filters' => [
            'status'   => 'Status',
            'employee' => 'Karyawan',
        ],
        'empty' => [
            'heading'     => 'Belum ada evaluasi',
            'description' => 'Buat evaluasi kinerja untuk seorang karyawan.',
        ],
    ],

    'ratings' => [
        'title' => 'Kriteria',
        'model' => 'Kriteria',
        'form'  => [
            'title'          => 'Kriteria',
            'weight'         => 'Bobot',
            'weight-helper'  => 'Bobot tidak harus berjumlah 100.',
            'score'          => 'Nilai',
            'score-helper'   => '1 paling rendah, 5 paling tinggi. Kosongkan jika belum dinilai.',
            'comment'        => 'Catatan',
        ],
        'empty' => [
            'heading'     => 'Belum ada kriteria',
            'description' => 'Tambahkan kriteria yang akan dinilai.',
        ],
    ],
];
