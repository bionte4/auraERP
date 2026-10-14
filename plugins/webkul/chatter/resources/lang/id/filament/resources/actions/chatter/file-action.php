<?php

return [
    'setup' => [
        'title'   => 'Lampiran',
        'tooltip' => 'Unggah Lampiran',

        'modal-submit-action-label' => 'Unggah',

        'form' => [
            'fields' => [
                'files'                  => 'Berkas',
                'attachment-helper-text' => 'Ukuran berkas maksimum: 10MB. Jenis yang diizinkan: Gambar, PDF, Word, Excel, Teks',

                'actions' => [
                    'delete' => [
                        'title' => 'Berkas dihapus',
                        'body'  => 'Berkas berhasil dihapus.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Lampiran Diunggah',
                    'body'  => 'Lampiran berhasil diunggah.',
                ],

                'warning'  => [
                    'title' => 'Tidak ada berkas baru',
                    'body'  => 'Semua berkas sudah diunggah.',
                ],

                'error' => [
                    'title' => 'Kesalahan unggah lampiran',
                    'body'  => 'Gagal mengunggah lampiran ',
                ],
            ],
        ],
    ],
];
