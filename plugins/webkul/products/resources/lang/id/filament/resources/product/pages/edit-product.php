<?php

return [
    'notification' => [
        'title' => 'Produk diperbarui',
        'body'  => 'Produk berhasil diperbarui.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Cetak Label',

            'form' => [
                'fields' => [
                    'quantity' => 'Jumlah Label',
                    'format'   => 'Format',

                    'format-options' => [
                        'dymo'       => 'Dymo',
                        '2x7_price'  => '2x7 dengan harga',
                        '4x7_price'  => '4x7 dengan harga',
                        '4x12'       => '4x12',
                        '4x12_price' => '4x12 dengan harga',
                    ],
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Produk dihapus',
                'body'  => 'Produk berhasil dihapus.',
            ],
        ],
    ],
];
