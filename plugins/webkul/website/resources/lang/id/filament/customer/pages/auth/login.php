<?php

return [
    'title'    => 'Masuk',
    'heading'  => 'Masuk',
    'messages' => [
        'failed' => 'Kredensial ini tidak cocok dengan data kami.',
    ],
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
        'password' => [
            'label' => 'Kata Sandi',
        ],
        'remember' => [
            'label' => 'Ingat saya',
        ],
        'actions' => [
            'authenticate' => [
                'label' => 'Masuk',
            ],
        ],
    ],
    'actions' => [
        'register' => [
            'before' => 'Belum memiliki akun?',
            'label'  => 'Buat akun',
        ],
        'request_password_reset' => [
            'label' => 'Lupa kata sandi?',
        ],
    ],
];
