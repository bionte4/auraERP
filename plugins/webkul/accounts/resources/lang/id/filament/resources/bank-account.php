<?php

return [
    'navigation' => [
        'title' => 'Rekening Bank',
        'group' => 'Bank',
    ],

    'form' => [
        'account-number'     => 'Nomor Rekening',
        'bank'               => [
            'title'    => 'Bank',
            'sections' => [
                'general' => [
                    'title' => 'Umum',

                    'fields' => [
                        'name'  => 'Nama',
                        'code'  => 'Kode Identifikasi Bank',
                        'email' => 'Email',
                        'phone' => 'Telepon',
                    ],
                ],

                'address' => [
                    'title' => 'Alamat',

                    'fields' => [
                        'address' => 'Alamat',
                        'city'    => 'Kota',
                        'street1' => 'Jalan 1',
                        'street2' => 'Jalan 2',
                        'state'   => 'Status',
                        'zip'     => 'Kode Pos',
                        'country' => 'Negara',
                    ],
                ],
            ],
        ],

        'account-holder'     => 'Pemegang Rekening',
    ],

    'table' => [
        'columns' => [
            'account-number' => 'Nomor Rekening',
            'bank'           => 'Bank',
            'account-holder' => 'Pemegang Rekening',
            'send-money'     => 'Dapat Mengirim Uang',
            'created-at'     => 'Dibuat Pada',
            'updated-at'     => 'Diperbarui Pada',
            'deleted-at'     => 'Dihapus Pada',
        ],

        'filters' => [
            'bank'           => 'Bank',
            'account-holder' => 'Pemegang Rekening',
            'creator'        => 'Pembuat',
            'can-send-money' => 'Dapat Mengirim Uang',
        ],

        'groups' => [
            'bank'               => 'Bank',
            'can-send-money'     => 'Dapat Mengirim Uang',
            'created-at'         => 'Dibuat Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Rekening bank diperbarui',
                    'body'  => 'Rekening bank berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Rekening bank dipulihkan',
                    'body'  => 'Rekening bank berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rekening bank dihapus',
                    'body'  => 'Rekening bank berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Rekening bank dihapus permanen',
                    'body'  => 'Rekening bank berhasil dihapus paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Rekening bank dipulihkan',
                    'body'  => 'Rekening bank berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rekening bank dihapus',
                    'body'  => 'Rekening bank berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Rekening bank dihapus permanen',
                    'body'  => 'Rekening bank berhasil dihapus paksa.',
                ],
            ],
        ],
    ],
];
