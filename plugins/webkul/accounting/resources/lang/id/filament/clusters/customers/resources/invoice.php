<?php

return [
    'title' => 'Faktur',

    'navigation' => [
        'title' => 'Faktur',
    ],

    'global-search' => [
        'customer' => 'Pelanggan',
        'date'     => 'Tanggal',
        'due-date' => 'Tanggal Jatuh Tempo',
        'amount'   => 'Jumlah',
    ],

    'form' => [
        'tabs' => [
            'invoice-lines' => [
                'repeater' => [
                    'products' => [
                        'actions' => [
                            'open-product' => [
                                'tooltip' => 'Buka produk',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
];
