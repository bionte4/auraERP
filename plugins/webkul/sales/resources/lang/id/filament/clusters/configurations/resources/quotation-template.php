<?php

return [
    'title' => 'Templat Penawaran',

    'navigation' => [
        'title'  => 'Templat Penawaran',
        'group'  => 'Pesanan Penjualan',
    ],

    'form' => [
        'tabs' => [
            'products' => [
                'title'  => 'Produk',
                'fields' => [
                    'products'     => 'Produk',
                    'name'         => 'Nama',
                    'quantity'     => 'Kuantitas',
                ],
            ],

            'terms-and-conditions' => [
                'title'  => 'Syarat & Ketentuan',
                'fields' => [
                    'note-placeholder' => 'Tuliskan syarat dan ketentuan untuk penawaran.',
                ],
            ],
        ],

        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'fields' => [
                    'name'               => 'Nama',
                    'quotation-validity' => 'Masa Berlaku Penawaran',
                    'sale-journal'       => 'Jurnal Penjualan',
                ],
            ],

            'signature-and-payment' => [
                'title' => 'Tanda Tangan & Pembayaran',

                'fields' => [
                    'online-signature'      => 'Tanda Tangan Daring',
                    'online-payment'        => 'Pembayaran Online',
                    'prepayment-percentage' => 'Persentase Uang Muka',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'created-by'            => 'Dibuat oleh',
            'company'               => 'Perusahaan',
            'name'                  => 'Nama',
            'number-of-days'        => 'Jumlah hari',
            'journal'               => 'Jurnal Penjualan',
            'signature-required'    => 'Tanda Tangan Diperlukan',
            'payment-required'      => 'Pembayaran Diperlukan',
            'prepayment-percentage' => 'Persentase Uang Muka',
        ],
        'groups'  => [
            'company' => 'Perusahaan',
            'name'    => 'Nama',
            'journal' => 'Jurnal',
        ],
        'filters' => [
            'created-by' => 'Dibuat Oleh',
            'company'    => 'Perusahaan',
            'name'       => 'Nama',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],
        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Templat penawaran dihapus',
                    'body'  => 'Templat penawaran berhasil dihapus.',
                ],
            ],

        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Templat penawaran dihapus',
                    'body'  => 'Templat penawaran berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'tabs' => [
            'products' => [
                'title' => 'Produk',
            ],
            'terms-and-conditions' => [
                'title' => 'Syarat & Ketentuan',
            ],
        ],
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',
            ],
            'signature_and_payment' => [
                'title' => 'Tanda Tangan & Pembayaran',
            ],
        ],
        'entries' => [
            'product'               => 'Produk',
            'description'           => 'Deskripsi',
            'quantity'              => 'Kuantitas',
            'unit-price'            => 'Harga Satuan',
            'section-name'          => 'Nama Bagian',
            'note-title'            => 'Judul Catatan',
            'name'                  => 'Nama Templat',
            'quotation-validity'    => 'Masa Berlaku Penawaran',
            'sale-journal'          => 'Jurnal Penjualan',
            'online-signature'      => 'Tanda Tangan Daring',
            'online-payment'        => 'Pembayaran Online',
            'prepayment-percentage' => 'Persentase Uang Muka',
        ],
    ],
];
