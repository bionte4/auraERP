<?php

return [
    'navigation' => [
        'title' => 'Daftar Material',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',
                'fields' => [
                    'reference'             => 'Referensi',
                    'reference-placeholder' => 'mis. BOM-001',
                    'product'               => 'Produk',
                    'product-variant'       => 'Varian Produk',
                    'quantity'              => 'Kuantitas',
                    'uom'                   => 'UOM',
                    'operation-type'        => 'Tipe Operasi',
                    'company'               => 'Perusahaan',
                    'type'                  => 'Tipe BoM',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Lain-lain',
                'fields' => [
                    'kit-information'                         => 'Informasi Kit',
                    'kit-information-content'                 => 'BoM kit digunakan untuk mengelompokkan komponen bagi transfer atau penjualan, bukan untuk diproduksi melalui perintah manufaktur.',
                    'manufacturing-lead-time'                 => 'Waktu Tunggu Manufaktur',
                    'days-to-prepare-manufacturing-order'     => 'Hari untuk menyiapkan Pesanan Manufaktur',
                    'days-suffix'                             => 'hari',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'      => 'Komponen',
                'add-action' => 'Tambah baris',
                'columns'    => [
                    'component'              => 'Komponen',
                    'apply-on-variants'      => 'Terapkan pada Varian',
                    'consumed-in-operation'  => 'Dikonsumsi dalam Operasi',
                    'highlight-consumption'  => 'Sorot Konsumsi',
                    'quantity'               => 'Kuantitas',
                    'uom'                    => 'Satuan Ukur Produk',
                ],
                'validation' => [
                    'component-different-from-product' => 'Komponen harus berbeda dari produk yang sedang diproduksi.',
                ],
                'create-form' => [
                    'fields' => [
                        'name'            => 'Nama',
                        'type'            => 'Tipe',
                        'category'        => 'Kategori',
                        'company'         => 'Perusahaan',
                        'uom'             => 'UOM',
                        'uom-placeholder' => 'UOM',
                    ],
                ],
            ],
            'operations' => [
                'title'      => 'Operasi',
                'add-action' => 'Tambah baris',
                'actions'    => [
                    'edit'                 => 'Ubah Operasi',
                    'copy-existing'        => 'Salin Operasi yang Ada',
                    'copy-existing-fields' => [
                        'operation' => 'Operasi',
                    ],
                ],
                'columns'    => [
                    'operation'        => 'Operasi',
                    'work-center'      => 'Pusat Kerja',
                    'time-mode'        => 'Perhitungan Durasi',
                    'time-mode-batch'  => 'Dihitung pada terakhir',
                    'company'          => 'Perusahaan',
                    'apply-on-variants'=> 'Terapkan pada Varian',
                    'duration'         => 'Durasi (menit)',
                ],
            ],
            'by-products' => [
                'title'      => 'Produk sampingan',
                'add-action' => 'Tambah baris',
                'columns'    => [
                    'product'   => 'Produk Sampingan',
                    'quantity'  => 'Kuantitas',
                    'uom'       => 'Satuan ukur',
                    'operation' => 'Diproduksi dalam Operasi',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Lain-lain',
                'fields' => [
                    'ready-to-produce'       => 'Kesiapan Manufaktur',
                    'routing'                => 'Routing',
                    'consumption'            => 'Konsumsi Fleksibel',
                    'operation-dependencies' => 'Ketergantungan Operasi',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'  => 'Referensi',
            'product'    => 'Produk',
            'quantity'   => 'Kuantitas',
            'uom'        => 'UOM',
            'type'       => 'Tipe BOM',
            'company'    => 'Perusahaan',
            'deleted-at' => 'Dihapus Pada',
            'updated-at' => 'Diperbarui Pada',
        ],
        'filters' => [
            'product' => 'Produk',
            'type'    => 'Tipe BOM',
            'company' => 'Perusahaan',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Daftar material dipulihkan',
                    'body'  => 'Daftar material berhasil dipulihkan.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Daftar material diarsipkan',
                    'body'  => 'Daftar material berhasil diarsipkan.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Daftar material dihapus',
                        'body'  => 'Daftar material telah dihapus secara permanen.',
                    ],
                    'error' => [
                        'title' => 'Daftar material tidak dapat dihapus',
                        'body'  => 'Daftar material tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Daftar material dipulihkan',
                    'body'  => 'Daftar material yang dipilih berhasil dipulihkan.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Daftar material diarsipkan',
                    'body'  => 'Daftar material yang dipilih berhasil diarsipkan.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Daftar material dihapus',
                        'body'  => 'Daftar material yang dipilih telah dihapus secara permanen.',
                    ],
                    'error' => [
                        'title' => 'Daftar material tidak dapat dihapus',
                        'body'  => 'Satu atau beberapa daftar material yang dipilih sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Informasi Umum',
                'entries' => [
                    'reference'      => 'Referensi',
                    'product'        => 'Produk',
                    'product-variant'=> 'Varian Produk',
                    'quantity'       => 'Kuantitas',
                    'uom'            => 'UOM',
                    'operation-type' => 'Tipe Operasi',
                    'company'        => 'Perusahaan',
                    'type'           => 'Tipe BoM',
                ],
            ],
            'record-information' => [
                'title'   => 'Informasi Rekaman',
                'entries' => [
                    'created-by'   => 'Dibuat Oleh',
                    'created-at'   => 'Dibuat Pada',
                    'last-updated' => 'Terakhir Diperbarui',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'   => 'Komponen',
                'entries' => [
                    'component' => 'Komponen',
                    'operation' => 'Operasi',
                    'quantity'  => 'Kuantitas',
                    'uom'       => 'Satuan Ukur Produk',
                ],
            ],
            'operations' => [
                'title'   => 'Operasi',
                'entries' => [
                    'operation'   => 'Operasi',
                    'work-center' => 'Pusat Kerja',
                    'time-mode'   => 'Perhitungan Durasi',
                    'duration'    => 'Durasi (menit)',
                ],
            ],
            'by-products' => [
                'title'   => 'Produk sampingan',
                'entries' => [
                    'product'   => 'Produk Sampingan',
                    'quantity'  => 'Kuantitas',
                    'uom'       => 'Satuan ukur',
                    'operation' => 'Diproduksi dalam Operasi',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Lain-lain',
                'entries' => [
                    'kit-information'                         => 'Informasi Kit',
                    'kit-information-content'                 => 'BoM kit digunakan untuk mengelompokkan komponen bagi transfer atau penjualan, bukan untuk diproduksi melalui perintah manufaktur.',
                    'ready-to-produce'                        => 'Kesiapan Manufaktur',
                    'routing'                                 => 'Routing',
                    'consumption'                             => 'Konsumsi Fleksibel',
                    'operation-dependencies'                  => 'Ketergantungan Operasi',
                    'manufacturing-lead-time'                 => 'Waktu Tunggu Manufaktur',
                    'days-to-prepare-manufacturing-order'     => 'Hari untuk menyiapkan Pesanan Manufaktur',
                    'days-suffix'                             => 'hari',
                ],
            ],
        ],
    ],
];
