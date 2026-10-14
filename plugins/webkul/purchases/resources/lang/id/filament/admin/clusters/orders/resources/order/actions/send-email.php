<?php

return [
    'label'        => 'Kirim melalui Email',
    'resend-label' => 'Kirim Ulang melalui Email',

    'form' => [
        'fields' => [
            'to'      => 'Ke',
            'subject' => 'Subjek',
            'message' => 'Pesan',
        ],
    ],

    'action' => [
        'notification' => [
            'success' => [
                'title' => 'Email terkirim',
                'body'  => 'Email berhasil dikirim.',
            ],

            'warning' => [
                'title' => 'Beberapa email tidak terkirim',
                'body'  => 'Beberapa pemasok tidak akan menerima email karena alamat email mereka tidak tersedia.',
            ],

            'danger' => [
                'title' => 'Email tidak terkirim',
                'body'  => 'Silakan tambahkan alamat email pada pemasok yang dipilih, lalu coba lagi.',
            ],
        ],
    ],
];
