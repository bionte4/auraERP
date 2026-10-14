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
            'variant-values'         => 'Nilai Varian',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Varian dihapus',
                    'body'  => 'Varian berhasil dihapus.',
                ],
            ],

            'view' => [
                'extra-footer-actions' => [
                    'print' => [
                        'label' => 'Cetak Label',

                        'form' => [
                            'fields' => [
                                'quantity' => 'Jumlah Label',
                                'format'   => 'Format',

                                'format-options' => [
                                    'dymo'       => 'Dymo',
                                    '2x7_price'  => '2x7 dengan harga',
                                    '4x7_price'  => '4x7 dengan harga',
                                    '4x12'       => '4x12',
                                    '4x12_price' => '4x12 dengan harga',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
