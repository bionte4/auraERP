<?php

return [
    'title' => 'Kelola Operasi',

    'form' => [
        'enable-work-orders' => [
            'label'       => 'Perintah Kerja',
            'helper-text' => 'Jalankan operasi di pusat kerja yang ditentukan.',
            'link-text'   => 'Konfigurasikan pusat kerja',
        ],

        'enable-work-order-dependencies' => [
            'label'       => 'Ketergantungan Perintah Kerja',
            'helper-text' => 'Atur urutan pemrosesan perintah kerja. Aktifkan fitur ini dari tab Lain-lain pada setiap BoM.',
        ],

        'enable-byproducts' => [
            'label'       => 'Produk Sampingan',
            'helper-text' => 'Hasilkan produk sampingan selama produksi (A + B → C + D).',
        ],
    ],
];
