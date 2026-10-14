<?php

return [
    'title' => 'Mata Uang',

    'navigation' => [
        'title' => 'Mata Uang',
    ],

    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Informasi Mata Uang',

                'fields' => [
                    'name'         => 'Nama Mata Uang',
                    'name-tooltip' => 'Masukkan nama resmi mata uang',
                    'symbol'       => 'Simbol Mata Uang',
                    'full-name'    => 'Nama Lengkap',
                    'iso-numeric'  => 'Kode Numerik ISO',
                ],
            ],

            'format-information' => [
                'title' => 'Konfigurasi Format',

                'fields' => [
                    'decimal-places'        => 'Tempat Desimal',
                    'rounding'              => 'Presisi Pembulatan',
                    'rounding-helper-text'  => 'Atur presisi pembulatan untuk perhitungan mata uang',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status & Konfigurasi',

                'fields' => [
                    'status' => 'Status',
                ],
            ],

            'rates' => [
                'title'       => 'Kurs Mata Uang',
                'description' => 'Kelola riwayat nilai tukar mata uang ini terhadap mata uang dasar (USD).',

                'fields' => [
                    'name'              => 'Tanggal',
                    'unit-per-currency' => 'Satuan per :currency',
                    'currency-per-unit' => ':currency per Satuan',
                ],

                'add-rate'   => 'Tambah Tarif',
                'item-label' => 'Tarif',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Nama Mata Uang',
            'symbol'         => 'Simbol',
            'full-name'      => 'Nama Lengkap',
            'iso-numeric'    => 'Kode ISO',
            'decimal-places' => 'Tempat Desimal',
            'rounding'       => 'Pembulatan',
            'status'         => 'Status',
            'created-at'     => 'Dibuat Pada',
            'updated-at'     => 'Diperbarui Pada',
        ],

        'groups' => [
            'name'           => 'Nama',
            'status'         => 'Status',
            'decimal-places' => 'Tempat Desimal',
            'creation-date'  => 'Tanggal Pembuatan',
            'last-update'    => 'Pembaruan Terakhir',
        ],

        'filters' => [
            'status' => 'Status',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title'   => 'Mata Uang dihapus',
                    'body'    => 'Mata uang berhasil dihapus.',

                    'success' => [
                        'title' => 'Mata Uang dihapus',
                        'body'  => 'Mata uang berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Mata uang tidak dapat dihapus',
                        'body'  => 'Mata uang tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],

            'deactivate' => [
                'notification' => [
                    'title' => 'Mata uang tidak dapat dinonaktifkan',
                    'body'  => 'Mata uang ini digunakan oleh satu atau beberapa perusahaan dan tidak dapat dinonaktifkan.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Mata uang dihapus',
                    'body'  => 'Mata uang berhasil dihapus.',

                    'error' => [
                        'title' => 'Mata uang tidak dapat dihapus',
                        'body'  => 'Beberapa mata uang yang dipilih sedang digunakan dan tidak dapat dihapus.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Informasi Mata Uang',

                'entries' => [
                    'name'         => 'Nama Mata Uang',
                    'symbol'       => 'Simbol Mata Uang',
                    'full-name'    => 'Nama Lengkap',
                    'iso-numeric'  => 'Kode Numerik ISO',
                ],
            ],

            'format-information' => [
                'title' => 'Konfigurasi Format',

                'entries' => [
                    'decimal-places' => 'Tempat Desimal',
                    'rounding'       => 'Presisi Pembulatan',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status & Konfigurasi',

                'entries' => [
                    'status' => 'Status',
                ],
            ],

            'rates' => [
                'title'       => 'Kurs Mata Uang',

                'entries' => [
                    'name'              => 'Tanggal',
                    'unit-per-currency' => 'Satuan per :currency',
                    'currency-per-unit' => ':currency per Satuan',
                ],
            ],
        ],
    ],
];
