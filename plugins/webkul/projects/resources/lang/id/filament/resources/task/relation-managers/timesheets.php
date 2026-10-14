<?php

return [
    'form' => [
        'date'                   => 'Tanggal',
        'employee'               => 'Karyawan',
        'description'            => 'Deskripsi',
        'time-spent'             => 'Waktu Terpakai',
        'time-spent-helper-text' => 'Waktu yang digunakan dalam jam (Mis. 1.5 jam berarti 1 jam 30 menit)',
    ],

    'table' => [
        'header-actions' => [
            'create' => [
                'label' => 'Tambah Lembar Waktu',

                'notification' => [
                    'title' => 'Lembar waktu dibuat',
                    'body'  => 'Lembar waktu berhasil dibuat.',
                ],
            ],
        ],

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
            'edit' => [
                'notification' => [
                    'title' => 'Lembar waktu diperbarui',
                    'body'  => 'Lembar waktu berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lembar waktu dihapus',
                    'body'  => 'Lembar waktu berhasil dihapus.',
                ],
            ],
        ],
    ],
];
