<?php

return [
    'title' => 'Varian',

    'form' => [
        'date'                   => 'Tanggal',
        'employee'               => 'Karyawan',
        'description'            => 'Deskripsi',
        'time-spent'             => 'Waktu Terpakai',
        'time-spent-helper-text' => 'Waktu yang digunakan dalam jam (Mis. 1.5 jam berarti 1 jam 30 menit)',
    ],

    'table' => [
        'columns' => [
            'date'                   => 'Tanggal',
            'employee'               => 'Karyawan',
            'description'            => 'Deskripsi',
            'time-spent'             => 'Waktu Terpakai',
            'time-spent-on-subtasks' => 'Waktu yang Dihabiskan pada Subtugas',
            'total-time-spent'       => 'Total Waktu Terpakai',
            'remaining-time'         => 'Waktu Tersisa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Varian dihapus',
                    'body'  => 'Varian berhasil dihapus.',
                ],
            ],
        ],
    ],
];
