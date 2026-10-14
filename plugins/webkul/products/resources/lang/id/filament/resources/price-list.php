<?php

return [
    'navigation' => [
        'title' => 'Daftar Harga',
        'group' => 'Produk',
    ],

    'header-actions' => [
        'create' => [
            'label' => 'Daftar Harga Baru',
        ],
    ],

    'form' => [
        'section' => [
            'general' => [
                'title' => 'Informasi Umum',

                'fields' => [
                    'name'     => 'Nama Daftar Harga',
                    'currency' => 'Mata Uang',
                    'company'  => 'Perusahaan',
                    'status'   => 'Aktif',
                ],
            ],

            'rules' => [
                'title'        => 'Aturan Harga',
                'description'  => 'Aturan pertama yang cocok dengan suatu produk menentukan harganya. Aturan yang lebih spesifik mengalahkan aturan yang lebih umum.',
                'add-rule'     => 'Tambah Aturan',
                'all-products' => 'Semua Produk',

                'actions' => [
                    'edit' => 'Ubah Aturan',
                ],

                'columns' => [
                    'apply-to'     => 'Terapkan Pada',
                    'target'       => 'Berlaku Untuk',
                    'min-quantity' => 'Min. Kuantitas',
                    'type'         => 'Hitung Harga',
                    'price'        => 'Harga',
                    'period'       => 'Periode',
                ],

                'formula' => [
                    'discount'     => 'diskon',
                    'markup'       => 'markup',
                    'rule-tip'     => ':base dengan :discount % :type dan biaya tambahan :surcharge'."\n".'Example: :amount * :factor + :surcharge → :total',
                    'rounding-tip' => 'Tips: ingin membulatkan pada 9.99? Bulatkan menjadi 10.00 dan atur biaya tambahan sebesar -0.01.',
                ],

                'fields' => [
                    'apply-to-product'      => 'Produk',
                    'apply-to-category'     => 'Kategori',
                    'all-products'          => 'Semua Produk',
                    'all-categories'        => 'Semua Kategori',
                    'all-variants'          => 'Semua Varian',
                    'variant'               => 'Varian',
                    'on'                    => 'pada',
                    'sales-price'           => 'Harga Jual',
                    'apply-to'              => 'Terapkan Ke',
                    'product'               => 'Produk',
                    'category'              => 'Kategori Produk',
                    'min-quantity'          => 'Kuantitas Min',
                    'type'                  => 'Jenis Harga',
                    'fixed-price'           => 'Harga Tetap',
                    'percent-price'         => 'Diskon',
                    'percent-price-helper'  => 'Gunakan nilai negatif untuk menerapkan markup.',
                    'base'                  => 'Harga dasar',
                    'base-price-list'       => 'Daftar Harga Lain',
                    'price-discount'        => 'Diskon',
                    'price-discount-helper' => 'Gunakan nilai negatif untuk menerapkan markup.',
                    'price-markup'          => 'Markup',
                    'price-round'           => 'Bulatkan ke',
                    'price-round-helper'    => 'Membulatkan harga ke kelipatan nilai ini, diterapkan setelah diskon dan sebelum biaya tambahan.',
                    'price-surcharge'       => 'Biaya Tambahan',
                    'price-min-margin'      => 'Margin Min.',
                    'price-max-margin'      => 'Margin Maks.',
                    'starts-at'             => 'Masa Berlaku',
                    'ends-at'               => 'Tanggal Akhir',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'     => 'Daftar Harga',
            'currency' => 'Mata Uang',
            'company'  => 'Perusahaan',
            'rules'    => 'Aturan',
            'status'   => 'Aktif',
        ],

        'filters' => [
            'status'   => 'Aktif',
            'currency' => 'Mata Uang',
        ],
    ],
];
