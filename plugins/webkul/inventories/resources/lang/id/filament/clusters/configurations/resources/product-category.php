<?php

return [
    'navigation' => [
        'title' => 'Kategori',
        'group' => 'Produk',
    ],

    'form' => [
        'sections' => [
            'inventory' => [
                'title' => 'Persediaan',

                'fieldsets' => [
                    'logistics' => [
                        'title' => 'Logistik',

                        'fields' => [
                            'routes' => 'Rute',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'inventory' => [
                'title' => 'Persediaan',

                'subsections' => [
                    'logistics' => [
                        'title' => 'Logistik',

                        'entries' => [
                            'routes'     => 'Rute Gudang',
                            'route_name' => 'Nama Rute',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
