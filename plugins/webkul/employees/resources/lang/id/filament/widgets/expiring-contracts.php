<?php

return [
    'heading' => [
        'title' => 'Kontrak Mendekati / Melewati Tanggal Berakhir',
    ],

    'table-columns' => [
        'employee'        => 'Karyawan',
        'department'      => 'Departemen',
        'job'             => 'Jabatan',
        'employment-type' => 'Tipe Karyawan',
        'manager'         => 'Atasan',
        'contract-end'    => 'Akhir Kontrak',
        'status'          => 'Status',
        'projects'        => 'Proyek',
    ],

    'status' => [
        'overdue'    => 'Berakhir :days hari lalu',
        'ends-today' => 'Berakhir hari ini',
        'days-left'  => 'Sisa :days hari',
    ],

    'filters' => [
        'window' => [
            'label'     => 'Rentang kontrak',
            'all'       => 'Semua (lewat + 3 bulan ke depan)',
            'expired'   => 'Sudah berakhir',
            'within-7'  => 'Dalam 7 hari',
            'within-30' => 'Dalam 30 hari',
            '31-90'     => '31–90 hari',
        ],
    ],

    'empty' => [
        'heading'     => 'Tidak ada kontrak mendekati tanggal berakhir',
        'description' => 'Karyawan aktif yang kontraknya berakhir dalam 3 bulan ke depan (atau sudah berakhir) akan muncul di sini.',
    ],
];
