<?php

return [
    'navigation' => [
        'title' => 'Pelaporan',
        'group' => 'Persediaan',
    ],

    'moves' => [
        'navigation' => [
            'title' => 'Riwayat Pergerakan',
        ],

        'filters' => [
            'product-category'     => 'Kategori Produk',
            'source-location'      => 'Lokasi Sumber',
            'destination-location' => 'Lokasi Tujuan',
            'package'              => 'Paket',
            'lot'                  => 'Nomor Lot/Seri',
            'package-type'         => 'Tipe Paket',
        ],

        'groups' => [
            'product'   => 'Produk',
            'status'    => 'Status',
            'date'      => 'Tanggal',
            'operation' => 'Operasi',
            'location'  => 'Lokasi',
            'category'  => 'Kategori',
        ],
    ],

    'quantities' => [
        'navigation' => [
            'title' => 'Lokasi',
        ],

        'filters' => [
            'warehouse'        => 'Gudang',
            'location'         => 'Lokasi',
            'product-category' => 'Kategori Produk',
            'storage-category' => 'Kategori Penyimpanan',
            'package'          => 'Paket',
            'lot'              => 'Nomor Lot/Seri',
            'package-type'     => 'Tipe Paket',
        ],

        'groups' => [
            'product'          => 'Produk',
            'product-category' => 'Kategori Produk',
            'location'         => 'Lokasi',
            'storage-category' => 'Kategori Penyimpanan',
            'lot'              => 'Nomor Lot/Seri',
            'package'          => 'Paket',
            'company'          => 'Perusahaan',
        ],
    ],
];
