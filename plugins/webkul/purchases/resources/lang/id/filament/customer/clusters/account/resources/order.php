<?php

return [
    'table' => [
        'columns' => [
            'reference'         => 'Referensi',
            'total-amount'      => 'Jumlah Total',
            'confirmation-date' => 'Tanggal Konfirmasi',
            'status'            => 'Status',
        ],
    ],

    'products' => [
        'columns' => [
            'product'    => 'Produk',
            'quantity'   => 'Kuantitas',
            'unit-price' => 'Harga Satuan',
            'taxes'      => 'Pajak',
            'discount'   => 'Diskon %',
            'amount'     => 'Jumlah',
        ],
    ],

    'infolist' => [
        'settings' => [
            'entries' => [
                'buyer' => 'Pembeli',
            ],

            'actions' => [
                'accept' => [
                    'label' => 'Terima',

                    'notification' => [
                        'title' => 'Penawaran Diterima',
                        'body'  => 'RFQ berhasil diakui.',
                    ],

                    'message' => [
                        'body' => 'RFQ telah diakui oleh pemasok.',
                    ],
                ],

                'decline' => [
                    'label' => 'Tolak',

                    'notification' => [
                        'title' => 'Penawaran Ditolak',
                        'body'  => 'RFQ berhasil ditolak.',
                    ],

                    'message' => [
                        'body' => 'RFQ telah ditolak oleh pemasok.',
                    ],
                ],

                'print' => [
                    'label' => 'Unduh/Cetak',
                ],
            ],
        ],

        'general' => [
            'entries' => [
                'purchase-order'        => 'Pesanan Pembelian #:id',
                'quotation'             => 'Permintaan Penawaran #:id',
                'order-date'            => 'Tanggal Pesanan',
                'from'                  => 'Dari',
                'confirmation-date'     => 'Tanggal Konfirmasi',
                'receipt-date'          => 'Tanggal Penerimaan',
                'products'              => 'Produk',
                'untaxed-amount'        => 'Jumlah Sebelum Pajak',
                'tax-amount'            => 'Jumlah Pajak',
                'total'                 => 'Total',
                'communication-history' => 'Riwayat Komunikasi',
            ],
        ],
    ],
];
