<?php

return [
    'label' => 'Label',

    'form' => [
        'fields' => [
            'type'          => 'Jenis Label',
            'quantity'      => 'Kuantitas',
            'format'        => 'Format',
            'layout'        => 'Tata Letak Label',
            'quantity-type' => 'Kuantitas untuk Dicetak',
            'quantity'      => 'Kuantitas',

            'quantity-type-options' => [
                'operation' => 'Kuantitas Operasi',
                'custom'    => 'Kuantitas Khusus',
                'per-slot'  => 'Satu per lot/SN',
                'per-unit'  => 'Satu per unit',
            ],

            'type-options' => [
                'product' => 'Label Produk',
                'lot'     => 'Label Lot/SN',
            ],

            'format-options' => [
                'dymo'       => 'Dymo',
                '2x7_price'  => '2x7 dengan harga',
                '4x7_price'  => '4x7 dengan harga',
                '4x12'       => '4x12',
                '4x12_price' => '4x12 dengan harga',
            ],
        ],
    ],
];
