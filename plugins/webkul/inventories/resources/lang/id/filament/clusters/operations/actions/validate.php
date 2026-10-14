<?php

return [
    'label'             => 'Validasi',
    'modal-heading'     => 'Buat Pesanan Susulan?',
    'modal-description' => 'Buat pesanan susulan jika produk yang tersisa akan diproses nanti. Jika tidak, jangan buat pesanan susulan.',

    'extra-modal-footer-actions' => [
        'no-backorder' => [
            'label' => 'Tanpa Pesanan Tertunda',
        ],
    ],

    'notification' => [
        'error' => [
            'title' => 'Validasi Gagal',
        ],

        'warning' => [
            'lines-missing' => [
                'title' => 'Tidak ada kuantitas yang dicadangkan',
                'body'  => 'Tidak ada kuantitas yang dicadangkan untuk transfer.',
            ],

            'no-quantities-reserved' => [
                'title' => 'Tidak ada kuantitas yang dicadangkan',
                'body'  => 'Tidak ada kuantitas yang dicadangkan untuk transfer.',
            ],

            'lot-missing' => [
                'title' => 'Nomor Lot/Seri Pasokan',
                'body'  => 'Anda perlu menyediakan Nomor Lot/Seri untuk produk :products.',
            ],

            'serial-qty' => [
                'title' => 'Nomor Seri Sudah Ditetapkan',
                'body'  => 'Nomor seri sudah ditetapkan ke produk lain.',
            ],

            'partial-package' => [
                'title' => 'Tidak dapat memindahkan isi paket yang sama',
                'body'  => 'Anda tidak dapat memindahkan isi paket yang sama lebih dari sekali dalam satu transfer atau membagi paket ke dua lokasi.',
            ],
        ],
    ],
];
