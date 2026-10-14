<?php

return [
    'navigation' => [
        'title' => 'Perintah Kerja',
        'group' => 'Operasi',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',
                'fields' => [
                    'work-order'          => 'Perintah Kerja',
                    'work-center'         => 'Pusat Kerja',
                    'product'             => 'Produk',
                    'quantity'            => 'Kuantitas',
                    'manufacturing-order' => 'Pesanan Manufaktur',
                    'lot-serial'          => 'Nomor Lot/Seri',
                    'start-date'          => 'Tanggal Mulai',
                    'end-date'            => 'Tanggal Akhir',
                    'date-range-separator'=> 'ke',
                    'expected-duration'   => 'Durasi yang Diharapkan',
                    'duration-suffix'     => 'menit',
                    'real-duration'       => 'Durasi Aktual',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'      => 'Pelacakan Waktu',
                'add-action' => 'Tambah baris',
                'columns'    => [
                    'user'         => 'Pengguna',
                    'duration'     => 'Durasi',
                    'start-date'   => 'Tanggal Mulai',
                    'end-date'     => 'Tanggal Akhir',
                    'productivity' => 'Produktivitas',
                ],
                'footer' => [
                    'real-duration' => 'Durasi Aktual',
                ],
            ],
            'components' => [
                'title'      => 'Komponen',
                'add-action' => 'Tambah baris',
                'columns'    => [
                    'product'    => 'Produk',
                    'from'       => 'Dari',
                    'to-consume' => 'Untuk Dikonsumsi',
                    'quantity'   => 'Kuantitas',
                    'uom'        => 'UoM',
                ],
            ],
            'work-instruction' => [
                'title'   => 'Instruksi Kerja',
                'entries' => [
                    'operation' => 'Operasi',
                    'worksheet' => 'Lembar Kerja',
                ],
            ],
            'blocked-by' => [
                'title'  => 'Diblokir Oleh',
                'fields' => [
                    'work-orders' => 'Perintah Kerja',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'operation'           => 'Operasi',
            'work-center'         => 'Pusat Kerja',
            'manufacturing-order' => 'Pesanan Manufaktur',
            'product'             => 'Produk',
            'quantity-remaining'  => 'Kuantitas Tersisa',
            'lot-serial'          => 'Lot/Serial',
            'start'               => 'Mulai',
            'end'                 => 'Akhir',
            'expected-duration'   => 'Durasi yang Diharapkan',
            'real-duration'       => 'Durasi Aktual',
            'status'              => 'Status',
        ],
        'groups' => [
            'status'              => 'Status',
            'work-center'         => 'Pusat Kerja',
            'manufacturing-order' => 'Pesanan Manufaktur',
            'product'             => 'Produk',
            'start'               => 'Mulai',
            'end'                 => 'Akhir',
        ],
        'filters' => [
            'work-order'          => 'Perintah Kerja',
            'status'              => 'Status',
            'operation'           => 'Operasi',
            'work-center'         => 'Pusat Kerja',
            'manufacturing-order' => 'Pesanan Manufaktur',
            'product'             => 'Produk',
            'start'               => 'Mulai',
            'end'                 => 'Akhir',
            'created-at'          => 'Dibuat Pada',
            'updated-at'          => 'Diperbarui Pada',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Umum',
                'entries' => [
                    'work-order'          => 'Perintah Kerja',
                    'work-center'         => 'Pusat Kerja',
                    'product'             => 'Produk',
                    'quantity'            => 'Kuantitas',
                    'manufacturing-order' => 'Pesanan Manufaktur',
                    'lot-serial'          => 'Nomor Lot/Seri',
                    'start-date'          => 'Tanggal Mulai',
                    'end-date'            => 'Tanggal Akhir',
                    'expected-duration'   => 'Durasi yang Diharapkan',
                    'real-duration'       => 'Durasi Aktual',
                ],
            ],
        ],
        'tabs' => [
            'time-tracking' => [
                'title'  => 'Pelacakan Waktu',
                'footer' => [
                    'real-duration' => 'Durasi Aktual',
                ],
            ],
            'components' => [
                'title' => 'Komponen',
            ],
            'work-instruction' => [
                'title'   => 'Instruksi Kerja',
                'entries' => [
                    'operation' => 'Operasi',
                    'worksheet' => 'Lembar Kerja',
                ],
            ],
            'blocked-by' => [
                'title'   => 'Diblokir Oleh',
                'columns' => [
                    'work-order'  => 'Perintah Kerja',
                    'work-center' => 'Pusat Kerja',
                    'status'      => 'Status',
                ],
            ],
        ],
    ],

    'pages' => [
        'list' => [
            'header-actions' => [
                'create' => [
                    'label' => 'Perintah Kerja Baru',
                ],
            ],
        ],
    ],
];
