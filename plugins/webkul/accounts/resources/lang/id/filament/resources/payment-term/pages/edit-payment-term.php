<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Termin pembayaran diperbarui',
            'body'  => 'Syarat pembayaran berhasil diperbarui.',
        ],

        'validation-error' => [
            'title' => 'Kesalahan Validasi',
            'body'  => 'Ketentuan jatuh tempo harus memiliki setidaknya satu baris persen dan jumlah persen harus 100%.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Termin pembayaran dihapus',
                'body'  => 'Syarat pembayaran berhasil dihapus.',
            ],
        ],
    ],
];
