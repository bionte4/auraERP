<?php

return [
    'form' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Entri Jurnal',

                'field-set' => [
                    'accounting-information' => [
                        'title'  => 'Informasi Akuntansi',
                        'fields' => [
                            'dedicated-credit-note-sequence' => 'Urutan Penomoran Nota Kredit Khusus',
                            'dedicated-payment-sequence'     => 'Urutan Penomoran Pembayaran Khusus',
                            'sort-code-placeholder'          => 'Masukkan kode jurnal',
                            'sort-code'                      => 'Urutkan',
                            'currency'                       => 'Mata Uang',
                            'color'                          => 'Warna',
                            'default-account'                => 'Akun Bawaan',
                            'profit-account'                 => 'Akun Laba',
                            'loss-account'                   => 'Akun Kerugian',
                            'suspense-account'               => 'Akun Penangguhan',
                            'bank-account'                   => 'Rekening Bank',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Nomor Rekening Bank',
                    ],
                ],
            ],

            'incoming-payments' => [
                'title'            => 'Pembayaran Masuk',
                'add-action-label' => 'Tambah Baris',

                'fields' => [
                    'payment-method'             => 'Metode Pembayaran',
                    'display-name'               => 'Nama Tampilan',
                    'account-number'             => 'Akun Penerimaan Belum Diselesaikan',
                    'relation-notes'             => 'Catatan Hubungan',
                    'relation-notes-placeholder' => 'Masukkan detail hubungan',
                ],
            ],

            'outgoing-payments' => [
                'title'            => 'Pembayaran Keluar',
                'add-action-label' => 'Tambah Baris',

                'fields' => [
                    'payment-method'             => 'Metode Pembayaran',
                    'display-name'               => 'Nama Tampilan',
                    'account-number'             => 'Akun Pembayaran Belum Diselesaikan',
                    'relation-notes'             => 'Catatan Hubungan',
                    'relation-notes-placeholder' => 'Masukkan detail hubungan',
                ],
            ],

            'advanced-settings' => [
                'title'  => 'Pengaturan Lanjutan',

                'fields' => [
                    'allowed-accounts'       => 'Akun yang Diizinkan',
                    'control-access'         => 'Kontrol Akses',
                    'payment-communication'  => 'Komunikasi Pembayaran',
                    'auto-check-on-post'     => 'Periksa Otomatis saat Posting',
                    'communication-type'     => 'Jenis Komunikasi',
                    'communication-standard' => 'Standar Komunikasi',
                ],
            ],
        ],

        'general' => [
            'title' => 'Informasi Umum',

            'fields' => [
                'name'    => 'Nama',
                'type'    => 'Tipe',
                'company' => 'Perusahaan',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'type'       => 'Tipe',
            'code'       => 'Kode',
            'currency'   => 'Mata Uang',
            'created-by' => 'Dibuat Oleh',
            'status'     => 'Status',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Jurnal dihapus',
                        'body'  => 'Jurnal berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Penghapusan jurnal gagal',
                        'body'  => 'Jurnal tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Jurnal dihapus',
                        'body'  => 'Jurnal berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Penghapusan jurnal gagal',
                        'body'  => 'Jurnal tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'journal-entries' => [
                'title' => 'Entri Jurnal',

                'field-set' => [
                    'accounting-information' => [
                        'title'   => 'Informasi Akuntansi',

                        'entries' => [
                            'dedicated-credit-note-sequence' => 'Urutan Penomoran Nota Kredit Khusus',
                            'dedicated-payment-sequence'     => 'Urutan Penomoran Pembayaran Khusus',
                            'sort-code-placeholder'          => 'Masukkan kode jurnal',
                            'sort-code'                      => 'Urutkan',
                            'currency'                       => 'Mata Uang',
                            'color'                          => 'Warna',
                            'default-account'                => 'Akun Bawaan',
                            'profit-account'                 => 'Akun Laba',
                            'loss-account'                   => 'Akun Kerugian',
                            'suspense-account'               => 'Akun Penangguhan',
                        ],
                    ],

                    'bank-account-number' => [
                        'title' => 'Nomor Rekening Bank',

                        'entries' => [
                            'account-number' => 'Nomor Rekening',
                        ],
                    ],
                ],
            ],

            'incoming-payments' => [
                'title' => 'Pembayaran Masuk',

                'entries' => [
                    'payment-method'             => 'Metode Pembayaran',
                    'display-name'               => 'Nama Tampilan',
                    'account-number'             => 'Akun Penerimaan Belum Diselesaikan',
                    'relation-notes'             => 'Catatan Hubungan',
                    'relation-notes-placeholder' => 'Masukkan detail hubungan',
                ],
            ],

            'outgoing-payments' => [
                'title' => 'Pembayaran Keluar',

                'entries' => [
                    'payment-method'             => 'Metode Pembayaran',
                    'display-name'               => 'Nama Tampilan',
                    'account-number'             => 'Akun Pembayaran Belum Diselesaikan',
                    'relation-notes'             => 'Catatan Hubungan',
                    'relation-notes-placeholder' => 'Masukkan detail hubungan',
                ],
            ],

            'advanced-settings' => [
                'title'   => 'Pengaturan Lanjutan',

                'allowed-accounts' => [
                    'title' => 'Akun yang Diizinkan',

                    'entries' => [
                        'allowed-accounts'       => 'Akun yang Diizinkan',
                        'control-access'         => 'Kontrol Akses',
                        'auto-check-on-post'     => 'Periksa Otomatis saat Posting',
                    ],
                ],

                'payment-communication'  => [
                    'title' => 'Komunikasi Pembayaran',

                    'entries' => [
                        'communication-type'     => 'Jenis Komunikasi',
                        'communication-standard' => 'Standar Komunikasi',
                    ],
                ],
            ],
        ],

        'general' => [
            'title' => 'Informasi Umum',

            'entries' => [
                'name'    => 'Nama',
                'type'    => 'Tipe',
                'company' => 'Perusahaan',
            ],
        ],
    ],

];
