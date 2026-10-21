<?php

return [
    'notification' => [
        'title'       => 'Pengguna diperbarui',
        'body'        => 'Pengguna berhasil diperbarui.',
        'error-title' => 'Pengguna tidak dapat disimpan',
        'error-body'  => 'Form gagal disimpan. Periksa detail kesalahan lalu coba lagi.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'Ubah Kata Sandi',

            'notification' => [
                'title' => 'Kata sandi diubah',
                'body'  => 'Kata sandi berhasil diubah.',
            ],

            'form' => [
                'new-password'         => 'Kata Sandi Baru',
                'confirm-new-password' => 'Konfirmasi Kata Sandi Baru',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Pengguna dihapus',
                'body'  => 'Pengguna berhasil dihapus.',
                'error' => [
                    'title' => 'Pengguna Tidak Dapat Dihapus',
                    'body'  => 'Ini adalah pengguna bawaan atau Anda tidak dapat menghapus diri sendiri.',
                ],
            ],
        ],
    ],
];
