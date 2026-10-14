<?php

return [
    'model'  => 'Absensi',
    'plural' => 'Absensi',

    'navigation' => [
        'title' => 'Absensi',
    ],

    'form' => [
        'section'         => 'Absensi',
        'employee'        => 'Karyawan',
        'date'            => 'Tanggal kerja',
        'status'          => 'Status',
        'check-in'        => 'Jam masuk',
        'check-out'       => 'Jam pulang',
        'note'            => 'Catatan',
        'late-helper'     => 'Absen masuk setelah pukul 09.00 ditandai terlambat.',
        'check-out-after' => 'Jam pulang harus setelah jam masuk.',
    ],

    'table' => [
        'columns' => [
            'date'               => 'Tanggal',
            'employee'           => 'Karyawan',
            'check-in'           => 'Masuk',
            'check-out'          => 'Pulang',
            'check-in-location'  => 'Lokasi masuk',
            'check-out-location' => 'Lokasi pulang',
            'worked'             => 'Durasi',
            'tracks'             => 'Jejak GPS',
            'status'             => 'Status',
            'note'               => 'Catatan',
        ],
        'groups' => [
            'date'     => 'Tanggal',
            'employee' => 'Karyawan',
        ],
        'filters' => [
            'status'   => 'Status',
            'employee' => 'Karyawan',
            'from'     => 'Dari tanggal',
            'until'    => 'Sampai tanggal',
        ],
        'empty' => [
            'heading'     => 'Belum ada absensi',
            'description' => 'Absen masuk untuk hari ini, atau catat absensi karyawan.',
        ],
    ],

    'tracks' => [
        'action'  => 'Jejak',
        'heading' => 'Jejak lokasi',
        'empty'   => 'Belum ada titik lokasi.',
    ],
];
