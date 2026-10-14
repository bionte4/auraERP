<?php

return [
    'setup' => [
        'title'        => 'Catat Catatan',
        'submit-title' => 'Log',

        'form' => [
            'fields' => [
                'hide-subject'            => 'Sembunyikan Subjek',
                'add-subject'             => 'Tambah Subjek',
                'subject'                 => 'Subjek',
                'write-message-here'      => 'Tulis pesan Anda di sini',
                'attachments-helper-text' => 'Ukuran berkas maksimum: 10MB. Jenis yang diizinkan: Gambar, PDF, Word, Excel, Teks',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Catatan log ditambahkan',
                    'body'  => 'Catatan log Anda berhasil ditambahkan.',
                ],

                'error' => [
                    'title' => 'Kesalahan penambahan log',
                    'body'  => 'Gagal menambahkan catatan log Anda',
                ],
            ],
        ],
    ],
];
