<?php

return [
    'label' => 'Buat Tagihan',

    'action' => [
        'notification' => [
            'warning' => [
                'title' => 'Tidak ada baris yang dapat difakturkan',
                'body'  => 'Tidak ada baris yang dapat difakturkan. Pastikan kuantitas telah diterima.',
            ],

            'missing-journal' => [
                'title' => 'Akuntansi belum disiapkan',
            ],

            'success' => [
                'title' => 'Tagihan dibuat',
                'body'  => 'Tagihan berhasil dibuat.',
            ],
        ],
    ],
];
