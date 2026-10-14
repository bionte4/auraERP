<?php

return [
    'label' => 'Retur',

    'modal' => [
        'form' => [
            'columns' => [
                'product'                 => 'Produk',
                'quantity'                => 'Kuantitas',
                'uom'                     => 'UOM',
                'excess-quantity-tooltip' => 'Kuantitas yang akan dikembalikan lebih besar daripada kuantitas yang diproses pada operasi asal.',
            ],
        ],
    ],

    'notification' => [
        'no-products' => [
            'body' => 'Tidak ada produk untuk dikembalikan (hanya baris dalam status Selesai dan belum dikembalikan seluruhnya yang dapat dikembalikan).',
        ],
        'no-quantities' => [
            'body' => 'Silakan tentukan minimal satu kuantitas yang bukan nol.',
        ],
    ],
];
