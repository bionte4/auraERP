<?php

return [
    'setup' => [
        'title'        => 'Kirim Pesan',
        'submit-title' => 'Kirim',

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
                    'title' => 'Pesan terkirim',
                    'body'  => 'Pesan Anda berhasil dikirim.',
                ],

                'error' => [
                    'title' => 'Kesalahan pengiriman pesan',
                    'body'  => 'Gagal mengirim pesan Anda',
                ],
            ],

            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
