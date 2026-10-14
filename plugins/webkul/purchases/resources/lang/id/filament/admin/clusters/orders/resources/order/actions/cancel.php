<?php

return [
    'label' => 'Batal',

    'action' => [
        'notification' => [
            'warning' => [
                'receipts' => [
                    'title' => 'Tidak dapat membatalkan pesanan',
                    'body'  => 'Pesanan tidak dapat dibatalkan karena memiliki penerimaan yang sudah selesai.',
                ],

                'bills' => [
                    'title' => 'Tidak dapat membatalkan pesanan',
                    'body'  => 'Pesanan tidak dapat dibatalkan. Anda harus terlebih dahulu membatalkan tagihan pemasok terkait.',
                ],
            ],

            'success' => [
                'title' => 'Pesanan dibatalkan',
                'body'  => 'Pesanan berhasil dibatalkan.',
            ],
        ],
    ],
];
