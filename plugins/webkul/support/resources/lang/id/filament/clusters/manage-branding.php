<?php

return [
    'breadcrumb' => 'Branding',
    'title'      => 'Branding',
    'group'      => 'Umum',

    'navigation' => [
        'label' => 'Branding',
    ],

    'form' => [
        'sections' => [
            'logo' => [
                'title'       => 'Logo & Favicon',
                'description' => 'Timpa logo, favicon, dan tinggi logo yang digunakan pada panel admin dan pelanggan. Biarkan bidang kosong untuk mempertahankan nilai bawaan.',
            ],
            'colors' => [
                'title'       => 'Warna',
                'description' => 'Timpa warna tema yang digunakan pada panel admin dan pelanggan. Biarkan warna kosong untuk mempertahankan nilai bawaan.',
            ],
        ],
        'fields' => [
            'light-logo'         => 'Logo Terang',
            'light-logo-helper'  => 'Ditampilkan pada latar belakang terang. Menggantikan logo bawaan.',
            'dark-logo'          => 'Logo Gelap',
            'dark-logo-helper'   => 'Ditampilkan saat mode gelap diaktifkan.',
            'favicon'            => 'Favicon',
            'favicon-helper'     => 'Ikon tab peramban.',
            'logo-height'        => 'Tinggi Logo',
            'logo-height-helper' => 'Nilai tinggi CSS, mis. 2rem atau 40px.',
            'primary-color'      => 'Utama',
            'gray-color'         => 'Abu-abu',
            'danger-color'       => 'Bahaya',
            'info-color'         => 'Info',
            'success-color'      => 'Berhasil',
            'warning-color'      => 'Peringatan',
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Atur ulang ke bawaan',
        ],
    ],
];
