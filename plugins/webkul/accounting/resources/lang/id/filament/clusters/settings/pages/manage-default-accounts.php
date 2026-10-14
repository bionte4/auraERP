<?php

return [
    'title' => 'Kelola Akun Bawaan',

    'setup' => [
        'action'       => 'Siapkan akuntansi untuk perusahaan ini',
        'notice'       => 'Akuntansi belum disiapkan untuk perusahaan ini. Gunakan "Siapkan akuntansi" di atas untuk membuat bagan akun, jurnal, dan pengaturan bawaannya.',
        'notification' => [
            'title' => 'Penyiapan akuntansi',
            'body'  => 'Bagan akun, jurnal, dan pengaturan bawaan telah dibuat untuk perusahaan ini.',
        ],
    ],

    'form' => [
        'exchange-difference-entries' => [
            'label' => 'Entri Selisih Kurs',

            'fields' => [
                'journal' => [
                    'label' => 'Jurnal',
                ],

                'gain' => [
                    'label' => 'Laba',
                ],

                'loss' => [
                    'label' => 'Rugi',
                ],
            ],
        ],

        'bank-transfer-and-payments' => [
            'label' => 'Transfer Bank dan Pembayaran',

            'fields' => [
                'bank-suspense-account' => [
                    'label' => 'Akun Suspens Bank',
                ],

                'transfer-account' => [
                    'label' => 'Akun Transfer',
                ],
            ],
        ],

        'product-accounts' => [
            'label' => 'Akun Produk',

            'fields' => [
                'income-account' => [
                    'label' => 'Akun Pendapatan',
                ],

                'expense-account' => [
                    'label' => 'Akun Beban',
                ],
            ],
        ],
    ],
];
