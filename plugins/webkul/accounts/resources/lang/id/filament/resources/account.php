<?php

return [
    'global-search' => [
        'code' => 'Kode',
        'type' => 'Tipe',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'code'                   => 'Kode',
                'account-name'           => 'Nama Akun',
                'accounting'             => 'Akuntansi',
                'account-type'           => 'Jenis Akun',
                'parent-account'         => 'Akun Induk',
                'parent-account-helper'  => 'Pilih akun yang sudah ada untuk menjadikan ini subakun.',
                'default-taxes'          => 'Pajak Bawaan',
                'tags'                   => 'Tag',
                'journals'               => 'Jurnal',
                'journals-helper'        => 'Disarankan secara otomatis berdasarkan Jenis Akun yang dipilih. Anda dapat mengganti pilihan tersebut.',
                'currency'               => 'Mata Uang',
                'deprecated'             => 'Usang',
                'reconcile'              => 'Izinkan Rekonsiliasi',
                'non-trade'              => 'Nonperdagangan',
                'companies'              => 'Perusahaan',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'code'           => 'Kode',
            'account-name'   => 'Nama Akun',
            'account-type'   => 'Akun',
            'parent-account' => 'Akun Induk',
            'currency'       => 'Mata Uang',
            'journals'       => 'Jurnal',
            'reconcile'      => 'Izinkan Rekonsiliasi',
        ],

        'grouping' => [
            'account-type' => 'Jenis Akun',
        ],

        'filters' => [
            'account-type'     => 'Jenis Akun',
            'parent-account'   => 'Akun Induk',
            'allow-reconcile'  => 'Izinkan Rekonsiliasi',
            'currency'         => 'Mata Uang',
            'account-journals' => 'Jurnal',
            'non-trade'        => 'Nonperdagangan',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Akun diperbarui',
                    'body'  => 'Akun berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Akun dihapus',
                        'body'  => 'Akun berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Penghapusan akun gagal',
                        'body'  => 'Akun tidak dapat dihapus karena memiliki item jurnal terkait.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Akun dihapus',
                        'body'  => 'Akun berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Penghapusan akun gagal',
                        'body'  => 'Akun tidak dapat dihapus karena memiliki item jurnal terkait.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'code'           => 'Kode',
                'account-name'   => 'Nama Akun',
                'accounting'     => 'Akuntansi',
                'account-type'   => 'Jenis Akun',
                'parent-account' => 'Akun Induk',
                'sub-accounts'   => 'Sub Akun',
                'default-taxes'  => 'Pajak Bawaan',
                'tags'           => 'Tag',
                'journals'       => 'Jurnal',
                'currency'       => 'Mata Uang',
                'deprecated'     => 'Usang',
                'reconcile'      => 'Rekonsiliasi',
                'non-trade'      => 'Nonperdagangan',
            ],
        ],
    ],
];
