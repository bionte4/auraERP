<?php

return [
    'title' => 'Lembar Waktu',

    'navigation' => [
        'title' => 'Lembar Waktu',
    ],

    'global-search' => [
        'project' => 'Proyek',
        'task'    => 'Tugas',
        'date'    => 'Tanggal',
    ],

    'form' => [
        'date'                   => 'Tanggal',
        'employee'               => 'Karyawan',
        'project'                => 'Proyek',
        'task'                   => 'Tugas',
        'description'            => 'Deskripsi',
        'time-spent'             => 'Waktu Terpakai',
        'time-spent-helper-text' => 'Waktu yang digunakan dalam jam (Mis. 1.5 jam berarti 1 jam 30 menit)',
    ],

    'table' => [
        'columns' => [
            'date'        => 'Tanggal',
            'employee'    => 'Karyawan',
            'project'     => 'Proyek',
            'task'        => 'Tugas',
            'description' => 'Deskripsi',
            'time-spent'  => 'Waktu Terpakai',
            'created-at'  => 'Dibuat Pada',
            'updated-at'  => 'Diperbarui Pada',
        ],

        'groups' => [
            'date'       => 'Tanggal',
            'employee'   => 'Karyawan',
            'project'    => 'Proyek',
            'task'       => 'Tugas',
            'creator'    => 'Pembuat',
        ],

        'filters' => [
            'date-from'  => 'Tanggal Dari',
            'date-until' => 'Tanggal Hingga',
            'employee'   => 'Karyawan',
            'project'    => 'Proyek',
            'task'       => 'Tugas',
            'creator'    => 'Pembuat',
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

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Lembar waktu dihapus',
                    'body'  => 'Lembar waktu berhasil dihapus.',
                ],
            ],
        ],
    ],
];
