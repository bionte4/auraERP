<?php

return [
    'navigation' => [
        'title' => 'Plugin',
    ],

    'tabs' => [
        'apps'          => 'Aplikasi',
        'extra'         => 'Tambahan',
        'installed'     => 'Terpasang',
        'not-installed' => 'Belum Terpasang',
    ],

    'header-actions' => [
        'sync' => [
            'label'                     => 'Sinkronkan Plugin yang Tersedia',
            'modal-heading'             => 'Sinkronkan Plugin',
            'modal-description'         => 'Ini akan memindai dan mendaftarkan plugin baru yang ditemukan.',
            'modal-submit-action-label' => 'Sinkronkan Plugin',

            'notification' => [
                'success' => [
                    'title' => 'Plugin Berhasil Disinkronkan',
                    'body'  => 'Menemukan dan menyinkronkan :count plugin baru.',
                ],

                'error' => [
                    'title' => 'Sinkronisasi Plugin Gagal',
                    'body'  => 'Terjadi kesalahan (:error) saat menyinkronkan plugin. Silakan coba lagi.',
                ],
            ],
        ],
    ],
];
