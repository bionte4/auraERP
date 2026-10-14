<?php

return [
    'title' => 'Cetak & Kirim',

    'modal' => [
        'title' => 'Pratinjau Faktur',

        'form' => [
            'partners'    => 'Pelanggan',
            'subject'     => 'Subjek',
            'description' => 'Deskripsi',
            'files'       => 'Lampiran',
        ],

        'action' => [
            'submit' => [
                'title' => 'Kirim',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Faktur Terkirim',
                'body'  => 'Faktur berhasil dikirim.',
            ],
        ],
    ],
];
