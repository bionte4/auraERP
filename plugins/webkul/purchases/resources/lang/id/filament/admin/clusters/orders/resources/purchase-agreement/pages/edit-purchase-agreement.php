<?php

return [
    'notification' => [
        'title' => 'Perjanjian Pembelian diperbarui',
        'body'  => 'Perjanjian pembelian berhasil diperbarui.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Konfirmasi',

            'notification' => [
                'unable' => [
                    'title' => 'Tidak dapat mengonfirmasi perjanjian pembelian',
                    'body'  => 'Tambahkan setidaknya satu baris produk sebelum mengonfirmasi perjanjian pembelian ini.',
                ],
            ],
        ],

        'close' => [
            'label'        => 'Tutup',
            'notification' => [
                'warning' => [
                    'title' => 'Tidak dapat menutup perjanjian pembelian',
                    'body'  => 'Anda tidak dapat menutup perjanjian pembelian ini karena beberapa RFQ terkait tidak berada dalam status Selesai atau Dibatalkan.',
                ],
            ],
        ],

        'cancel' => [
            'label' => 'Batal',
        ],

        'print' => [
            'label' => 'Cetak',
        ],

        'delete' => [
            'notification' => [
                'title' => 'Perjanjian Pembelian dihapus',
                'body'  => 'Perjanjian pembelian berhasil dihapus.',
            ],
        ],
    ],
];
