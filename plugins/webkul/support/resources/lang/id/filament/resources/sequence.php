<?php

return [
    'model-label' => 'Urutan',

    'plural-model-label' => 'Urutan',

    'navigation' => [
        'title' => 'Urutan',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'       => 'Umum',
                'description' => 'Penomoran untuk jurnal, gudang, dan jenis operasi dibuat secara otomatis saat data tersebut dibuat — ubah di sini. Pembuatan manual hanya diperlukan untuk penomoran berbasis kode khusus.',

                'fields' => [
                    'name'      => 'Nama',
                    'code'      => 'Kode',
                    'code-help' => 'Pengenal teknis yang digunakan oleh dokumen, mis. sales.order. Penomoran yang dibuat otomatis sudah memiliki kode yang tepat.',
                    'company'   => 'Perusahaan',
                ],
            ],

            'format' => [
                'title' => 'Penomoran',

                'fields' => [
                    'prefix'           => 'Awalan',
                    'prefix-help'      => 'Placeholder: %(year), %(y), %(month), %(day). Contoh: INV/%(year)/',
                    'suffix'           => 'Akhiran',
                    'padding'          => 'Pengisi Nomor',
                    'next-number'      => 'Nomor Berikutnya',
                    'next-number-help' => 'Hanya dapat dinaikkan. Untuk memulai ulang penomoran dengan aman, hapus penomoran tersebut — penomoran akan dibuat ulang dari nomor dokumen tertinggi yang ada.',
                    'step'             => 'Langkah',
                    'reset-frequency'  => 'Atur Ulang Penghitung',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'            => 'Nama',
            'code'            => 'Kode',
            'applies-to'      => 'Berlaku Untuk',
            'company'         => 'Perusahaan',
            'next-preview'    => 'Nomor Dokumen Berikutnya',
            'next-number'     => 'Nomor Berikutnya',
            'reset-frequency' => 'Atur Ulang Penghitung',
        ],

        'variants' => [
            'refund'         => 'Pengembalian Dana',
            'payment'        => 'Pembayaran',
            'refund-payment' => 'Pengembalian Dana + Pembayaran',
        ],

        'filters' => [
            'company' => 'Perusahaan',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Urutan diperbarui',
                    'body'  => 'Penomoran berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Urutan dihapus',
                    'body'  => 'Penomoran berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Penomoran dihapus',
                    'body'  => 'Penomoran berhasil dihapus.',
                ],
            ],
        ],
    ],
];
