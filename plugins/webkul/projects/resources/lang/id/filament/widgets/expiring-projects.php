<?php

return [
    'heading' => [
        'title' => 'Proyek Mendekati / Melewati Tenggat',
    ],

    'table-columns' => [
        'project'         => 'Proyek',
        'customer'        => 'Customer',
        'project-manager' => 'Manajer Proyek',
        'end-date'        => 'Tanggal Berakhir',
        'status'          => 'Status',
        'progress'        => 'Progress Tugas',
        'overdue-tasks'   => 'Tugas Terlambat',
    ],

    'status' => [
        'overdue'    => 'Terlambat :days hari',
        'ends-today' => 'Berakhir hari ini',
        'days-left'  => 'Sisa :days hari',
    ],

    'filters' => [
        'window' => [
            'label'     => 'Rentang tenggat',
            'all'       => 'Semua (lewat + 3 bulan ke depan)',
            'expired'   => 'Sudah berakhir',
            'within-30' => 'Dalam 30 hari',
            '31-60'     => '31–60 hari',
            '61-90'     => '61–90 hari',
        ],
    ],

    'empty' => [
        'heading'     => 'Tidak ada proyek mendekati tenggat',
        'description' => 'Proyek aktif yang berakhir dalam 3 bulan ke depan (atau sudah melewati tanggal berakhir) akan muncul di sini.',
    ],
];
