<?php

return [
    'group' => [
        'label' => 'Akses Portal',
    ],

    'grant' => [
        'label' => 'Berikan Akses Portal',

        'modal' => [
            'heading'     => 'Berikan Akses Portal',
            'description' => 'Email undangan akan dikirim ke :email beserta tautan untuk mengatur kata sandi portal pelanggan.',
        ],

        'notification' => [
            'email-missing' => [
                'title' => 'Alamat email wajib diisi',
                'body'  => 'Tambahkan alamat email pada kontak ini sebelum memberikan akses portal.',
            ],

            'email-taken' => [
                'title' => 'Alamat email sudah digunakan',
                'body'  => 'Kontak lain yang memiliki akses portal sudah menggunakan alamat email ini.',
            ],

            'granted' => [
                'title' => 'Akses portal diberikan',
                'body'  => 'Undangan untuk mengatur kata sandi telah dikirim ke :email.',
            ],

            'email-failed' => [
                'title' => 'Akses portal diberikan, tetapi email undangan tidak dapat dikirim',
            ],
        ],
    ],

    'change-password' => [
        'label' => 'Ubah Kata Sandi',

        'modal' => [
            'heading'     => 'Ubah Kata Sandi Portal',
            'description' => 'Tetapkan kata sandi portal pelanggan yang baru untuk :email.',
        ],

        'form' => [
            'password' => [
                'label' => 'Kata Sandi Baru',
            ],

            'password-confirmation' => [
                'label' => 'Konfirmasi Kata Sandi Baru',
            ],
        ],

        'notification' => [
            'changed' => [
                'title' => 'Kata sandi portal diubah',
                'body'  => 'Kontak sekarang dapat masuk ke portal pelanggan dengan kata sandi baru.',
            ],
        ],
    ],

    'password-reset' => [
        'label' => 'Kirim Atur Ulang Kata Sandi',

        'modal' => [
            'heading'     => 'Kirim Atur Ulang Kata Sandi',
            'description' => 'Tautan pengaturan ulang kata sandi akan dikirim ke :email.',
        ],

        'notification' => [
            'sent' => [
                'title' => 'Tautan pengaturan ulang kata sandi terkirim',
                'body'  => 'Tautan pengaturan ulang kata sandi telah dikirim ke :email.',
            ],

            'failed' => [
                'title' => 'Tautan pengaturan ulang kata sandi tidak dapat dikirim',
            ],
        ],
    ],

    'revoke' => [
        'label' => 'Cabut Akses Portal',

        'modal' => [
            'heading'     => 'Cabut Akses Portal',
            'description' => 'Kontak tidak akan dapat lagi masuk ke portal pelanggan. Tautan pengaturan ulang kata sandi yang masih tertunda akan menjadi tidak berlaku.',
        ],

        'notification' => [
            'revoked' => [
                'title' => 'Akses portal dicabut',
                'body'  => 'Kontak tidak lagi dapat masuk ke portal pelanggan.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'portal-access' => 'Akses Portal',
        ],

        'filters' => [
            'portal-access' => 'Akses Portal',
        ],
    ],

    'infolist' => [
        'section' => [
            'title' => 'Portal Pelanggan',
        ],

        'entries' => [
            'status' => [
                'label'   => 'Akses Portal',
                'granted' => 'Diberikan',
                'none'    => 'Tidak Diberikan',
            ],

            'email-verified-at' => [
                'label'       => 'Email Diverifikasi Pada',
                'placeholder' => 'Tidak Pernah',
            ],

            'last-login-at' => [
                'label'       => 'Login Portal Terakhir',
                'placeholder' => 'Tidak Pernah',
            ],
        ],
    ],
];
