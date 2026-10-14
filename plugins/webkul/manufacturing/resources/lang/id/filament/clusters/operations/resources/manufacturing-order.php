<?php

return [
    'navigation' => [
        'title' => 'Pesanan Manufaktur',
        'group' => 'Operasi',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',
                'fields' => [
                    'product'                => 'Produk',
                    'quantity'               => 'Kuantitas',
                    'uom'                    => 'UoM',
                    'bill-of-material'       => 'Daftar Material',
                    'scheduled-date'         => 'Tanggal Terjadwal',
                    'scheduled-end'          => 'Akhir Jadwal',
                    'responsible'            => 'Penanggung Jawab',
                    'to-produce'             => 'Untuk Diproduksi',
                    'to-produce-placeholder' => 'Pratinjau gambar',
                    'uom-placeholder'        => 'UoM',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Komponen',
                'add-action'   => 'Tambah baris',
                'process-note' => 'Komponen akan dibuat seiring proses manufaktur disusun.',
                'columns'      => [
                    'component'          => 'Produk',
                    'from'               => 'Dari',
                    'to-consume'         => 'Untuk Dikonsumsi',
                    'to-consume-tooltip' => 'Kuantitas yang tersedia tidak mencukupi',
                    'quantity'           => 'Kuantitas',
                    'uom'                => 'UoM',
                    'forecast'           => 'Perkiraan',
                ],
            ],
            'work-orders' => [
                'title'        => 'Perintah Kerja',
                'add-action'   => 'Tambah baris',
                'process-note' => 'Perintah kerja akan dibuat setelah proses manufaktur dikonfigurasi.',
                'columns'      => [
                    'operation'          => 'Operasi',
                    'work-center'        => 'Pusat Kerja',
                    'product'            => 'Produk',
                    'quantity-remaining' => 'Kuantitas Tersisa',
                    'quantity-produced'  => 'Kuantitas Diproduksi',
                    'start'              => 'Mulai',
                    'end'                => 'Akhir',
                    'expected-duration'  => 'Durasi yang Diharapkan',
                    'real-duration'      => 'Durasi Aktual',
                    'status'             => 'Status',
                    'lot-serial'         => 'Lot/Serial',
                ],
                'actions'      => [
                    'open-work-order' => [
                        'tooltip' => 'Buka perintah kerja',
                    ],

                    'done' => [
                        'label' => 'Selesai',
                    ],
                ],
            ],
            'by-products' => [
                'title'        => 'Produk Sampingan',
                'process-note' => 'Produk sampingan akan dibuat seiring proses manufaktur disusun.',
                'columns'      => [
                    'product'    => 'Produk',
                    'to'         => 'Ke',
                    'to-produce' => 'Untuk Diproduksi',
                    'uom'        => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Lain-lain',
                'fields' => [
                    'operation-type'             => 'Tipe Operasi',
                    'source'                     => 'Sumber',
                    'finished-products-location' => 'Lokasi Produk Jadi',
                    'company'                    => 'Perusahaan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'              => 'Referensi',
            'start'                  => 'Mulai',
            'end'                    => 'Akhir',
            'deadline'               => 'Batas Waktu',
            'product'                => 'Produk',
            'lot-serial-number'      => 'Nomor Lot/Seri',
            'bill-of-material'       => 'Daftar Material',
            'source'                 => 'Sumber',
            'responsible'            => 'Penanggung Jawab',
            'mo-readiness'           => 'Kesiapan Pesanan Manufaktur',
            'component-status'       => 'Status Komponen',
            'quantity'               => 'Kuantitas',
            'uom'                    => 'UoM',
            'consumption-efficiency' => 'Efisiensi Konsumsi',
            'expected-duration'      => 'Durasi yang Diharapkan',
            'real-duration'          => 'Durasi Aktual',
            'company'                => 'Perusahaan',
            'state'                  => 'Status',
        ],
        'groups' => [
            'state'            => 'Status',
            'product'          => 'Produk',
            'bill-of-material' => 'Daftar Material',
            'responsible'      => 'Penanggung Jawab',
            'deadline'         => 'Batas Waktu',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Umum',
                'entries' => [
                    'product'                  => 'Produk',
                    'scheduled-date'           => 'Tanggal Terjadwal',
                    'responsible'              => 'Penanggung Jawab',
                    'quantity'                 => 'Kuantitas',
                    'uom'                      => 'UoM',
                    'bill-of-material'         => 'Daftar Material',
                    'operation-type'           => 'Tipe Operasi',
                    'consumption-efficiency'   => 'Efisiensi Konsumsi',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Komponen',
                'process-note' => 'Komponen akan tersedia setelah proses manufaktur dikonfigurasi.',
                'columns'      => [
                    'component' => 'Komponen',
                    'quantity'  => 'Kuantitas',
                    'uom'       => 'UoM',
                ],
            ],
            'work-orders' => [
                'title'        => 'Perintah Kerja',
                'process-note' => 'Perintah kerja akan tersedia setelah proses manufaktur dikonfigurasi.',
                'columns'      => [
                    'operation'          => 'Operasi',
                    'work-center'        => 'Pusat Kerja',
                    'product'            => 'Produk',
                    'quantity-remaining' => 'Kuantitas Tersisa',
                    'expected-duration'  => 'Durasi yang Diharapkan',
                    'real-duration'      => 'Durasi Aktual',
                    'lot-serial'         => 'Lot/Serial',
                    'start'              => 'Mulai',
                    'end'                => 'Akhir',
                ],
            ],
            'by-products' => [
                'title'        => 'Produk Sampingan',
                'process-note' => 'Produk sampingan akan tersedia setelah proses manufaktur dikonfigurasi.',
                'columns'      => [
                    'product'    => 'Produk',
                    'to'         => 'Ke',
                    'to-produce' => 'Untuk Diproduksi',
                    'uom'        => 'UoM',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Lain-lain',
                'entries' => [
                    'operation-type'             => 'Tipe Operasi',
                    'source'                     => 'Sumber',
                    'finished-products-location' => 'Lokasi Produk Jadi',
                    'company'                    => 'Perusahaan',
                ],
            ],
        ],
    ],

    'pages' => [
        'shared' => [
            'header-actions' => [
                'confirm' => [
                    'label'        => 'Konfirmasi',
                    'notification' => [
                        'title' => 'Pesanan manufaktur dikonfirmasi',
                    ],
                ],

                'cancel' => [
                    'label'        => 'Batal',
                    'notification' => [
                        'title' => 'Pesanan manufaktur dibatalkan',
                    ],
                ],
            ],
        ],
    ],
];
