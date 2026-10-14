<?php

return [
    'tabs' => [
        'all'      => 'Semua Pengguna',
        'archived' => 'Pengguna Diarsipkan',
    ],

    'header-actions' => [
        'invite' => [
            'title' => 'Undang Pengguna',
            'modal' => [
                'submit-action-label' => 'Undang Pengguna',
            ],
            'form' => [
                'email' => 'Email',
            ],
            'notification' => [
                'success' => [
                    'title' => 'Pengguna diundang',
                    'body'  => 'Pengguna berhasil diundang',
                ],
                'error' => [
                    'title' => 'Undangan Pengguna Gagal',
                    'body'  => 'Sistem mengalami kesalahan yang tidak terduga saat mencoba mengirim undangan pengguna.',
                ],

                'default-company-error' => [
                    'title' => 'Perusahaan Bawaan Belum Ditetapkan',
                    'body'  => 'Silakan atur perusahaan bawaan dari pengaturan sebelum mengundang pengguna.',
                ],
            ],
        ],

        'create' => [
            'label' => 'Pengguna Baru',
        ],
    ],
];
