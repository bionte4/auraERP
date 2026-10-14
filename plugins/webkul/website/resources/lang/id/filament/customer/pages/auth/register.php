<?php

return [
    'title'         => 'Daftar',
    'heading'       => 'Daftar',
    'notifications' => [
        'throttled' => [
            'title' => 'Terlalu banyak percobaan. Coba lagi dalam :seconds detik.',
            'body'  => 'Harap tunggu :seconds detik (:minutes menit) sebelum mencoba lagi.',
        ],
    ],
    'form' => [
        'name' => [
            'label' => 'Nama',
        ],
        'email' => [
            'label' => 'Alamat email',
        ],
        'password' => [
            'label'                => 'Kata Sandi',
            'validation_attribute' => 'kata sandi',
        ],
        'password_confirmation' => [
            'label' => 'Konfirmasi kata sandi',
        ],
        'actions' => [
            'register' => [
                'label' => 'Buat akun',
            ],
        ],
    ],
    'actions' => [
        'login' => [
            'before' => 'Sudah memiliki akun?',
            'label'  => 'Masuk',
        ],
    ],
];
