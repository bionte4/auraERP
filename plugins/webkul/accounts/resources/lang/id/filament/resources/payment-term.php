<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'payment-term'         => 'Termin Pembayaran',
                'company'              => 'Perusahaan',
                'company-placeholder'  => 'Semua Perusahaan',
                'early-discount'       => 'Diskon Awal',
                'discount-days-prefix' => 'jika dibayar dalam',
                'discount-days-suffix' => 'hari',
                'reduced-tax'          => 'Pajak dikurangi',
                'note'                 => 'Catatan',
                'status'               => 'Status',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Syarat Jatuh Tempo',

                'repeater' => [
                    'due-terms' => [
                        'fields' => [
                            'value'                  => 'Nilai',
                            'due'                    => 'Jatuh Tempo',
                            'delay-type'             => 'Jenis Penundaan',
                            'days-on-the-next-month' => 'Hari pada bulan berikutnya',
                            'days'                   => 'Hari',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'payment-term' => 'Termin Pembayaran',
            'company'      => 'Perusahaan',
            'created-at'   => 'Dibuat Pada',
            'updated-at'   => 'Diperbarui Pada',
        ],

        'groups' => [
            'company-name'        => 'Nama Perusahaan',
            'discount-days'       => 'Hari Diskon',
            'early-pay-discount'  => 'Diskon Pembayaran Awal',
            'payment-term'        => 'Termin Pembayaran',
            'display-on-invoice'  => 'Tampilkan di Faktur',
            'early-discount'      => 'Diskon Awal',
            'discount-percentage' => 'Persentase Diskon',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Termin Pembayaran dipulihkan',
                    'body'  => 'Syarat pembayaran berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Termin Pembayaran dihapus',
                    'body'  => 'Syarat pembayaran berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Syarat Pembayaran dihapus permanen',
                        'body'  => 'Ketentuan pembayaran berhasil dihapus paksa.',
                    ],

                    'error' => [
                        'title' => 'Penghapusan permanen syarat pembayaran gagal',
                        'body'  => 'Ketentuan pembayaran tidak dapat dihapus paksa karena terkait dengan entri jurnal.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Syarat Pembayaran dipulihkan',
                    'body'  => 'Ketentuan pembayaran berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Termin Pembayaran dihapus',
                    'body'  => 'Syarat pembayaran berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Syarat Pembayaran dihapus permanen',
                        'body'  => 'Ketentuan pembayaran berhasil dihapus paksa.',
                    ],

                    'error' => [
                        'title' => 'Penghapusan paksa syarat pembayaran gagal',
                        'body'  => 'Ketentuan pembayaran tidak dapat dihapus paksa karena memiliki entri jurnal terkait.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'payment-term'         => 'Termin Pembayaran',
                'early-discount'       => 'Diskon Awal',
                'discount-percentage'  => 'Persentase Diskon',
                'discount-days-prefix' => 'jika dibayar dalam',
                'discount-days-suffix' => 'hari',
                'reduced-tax'          => 'Pajak dikurangi',
                'note'                 => 'Catatan',
                'status'               => 'Status',
            ],
        ],

        'tabs' => [
            'due-terms' => [
                'title' => 'Syarat Jatuh Tempo',

                'repeater' => [
                    'due-terms' => [
                        'entries' => [
                            'value'                  => 'Nilai',
                            'due'                    => 'Jatuh Tempo',
                            'delay-type'             => 'Jenis Penundaan',
                            'days-on-the-next-month' => 'Hari pada bulan berikutnya',
                            'days'                   => 'Hari',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
