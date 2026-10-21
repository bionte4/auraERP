<?php

return [
    'notification' => [
        'title'       => 'Pengguna diperbarui',
        'body'        => 'Pengguna berhasil diperbarui.',
        'error-title' => 'Pengguna tidak dapat disimpan',
        'error-body'  => 'Form gagal disimpan. Cari tim yang dibutuhkan, jangan tampilkan semua sekaligus, lalu simpan lagi.',
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
