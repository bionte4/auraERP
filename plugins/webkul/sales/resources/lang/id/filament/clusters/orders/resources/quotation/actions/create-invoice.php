<?php

return [
    'title' => 'Buat Faktur',

    'modal' => [
        'heading' => 'Buat Faktur',
    ],

    'notification' => [
        'invoice-created' => [
            'title' => 'Faktur dibuat',
            'body'  => 'Faktur berhasil dibuat.',
        ],

        'no-invoiceable-lines' => [
            'title' => 'Tidak ada baris yang dapat difakturkan',
            'body'  => 'Tidak ada baris yang dapat difakturkan. Pastikan kuantitas telah diterima.',
        ],
    ],

    'form' => [
        'fields' => [
            'create-invoice' => 'Buat Faktur',
        ],
    ],
];
