<?php

return [
    'title' => 'Bayar',

    'form' => [
        'fields' => [
            'journal'                => 'Jurnal',
            'amount'                 => 'Jumlah',
            'currency'               => 'Mata Uang',
            'payment-method-line'    => 'Baris Metode Pembayaran',
            'payment-date'           => 'Tanggal Pembayaran',
            'recipient-bank-account' => 'Rekening Bank Penerima',
            'communication'          => 'Memo',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title' => 'Pembayaran Gagal',
        ],
    ],
];
