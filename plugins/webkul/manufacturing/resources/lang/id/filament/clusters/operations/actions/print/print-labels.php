<?php

return [
    'label' => 'Cetak Label',

    'form' => [
        'fields' => [
            'quantity'      => 'Kuantitas',
            'format'        => 'Format',
            'quantity-type' => 'Kuantitas untuk Dicetak',

            'quantity-type-options' => [
                'operation' => 'Kuantitas Operasi',
                'custom'    => 'Kuantitas Khusus',
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
