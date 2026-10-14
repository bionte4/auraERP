<?php

return [
    'title'         => 'Lupa kata sandi',
    'heading'       => 'Lupa kata sandi',
    'notifications' => [
        'throttled' => [
            'title' => 'Terlalu banyak percobaan. Coba lagi dalam :seconds detik.',
            'body'  => 'Harap tunggu :seconds detik (:minutes menit) sebelum mencoba lagi.',
        ],
    ],
    'form' => [
        'email' => [
            'label' => 'Alamat email',
        ],
        'actions' => [
            'request' => [
                'label' => 'Kirim tautan pengaturan ulang',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'label' => 'Kembali ke masuk',
        ],
    ],
];
