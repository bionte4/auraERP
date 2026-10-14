<?php

return [
    'post-action-validate' => [
        'customer-required'    => 'Harap berikan Pelanggan yang valid untuk melanjutkan validasi Faktur Pelanggan.',
        'vendor-required'      => 'Harap berikan Pemasok yang valid untuk melanjutkan validasi Tagihan Pemasok.',
        'bank-archived'        => 'Bank Mitra yang dipilih dan terlampir pada faktur ini telah diarsipkan.',
        'negative-amount'      => 'Faktur tidak dapat dikonfirmasi dengan jumlah total yang negatif.',
        'date-required'        => 'Harap berikan Tanggal Tagihan/Pengembalian Dana yang valid untuk melanjutkan validasi Tagihan/Pengembalian Dana.',
        'currency-archived'    => 'Anda tidak dapat mengonfirmasi faktur dengan mata uang yang diarsipkan.',
        'account-deprecated'   => 'Satu atau beberapa baris pada faktur ini menggunakan akun yang sudah usang.',
        'lines-required'       => 'Silakan tambahkan minimal satu baris pada faktur.',
        'draft-state-required' => 'Hanya faktur berstatus draf yang dapat dikonfirmasi.',
        'journal-archived'     => 'Anda tidak dapat mengonfirmasi faktur dengan jurnal yang diarsipkan.',
    ],

    'documents' => [
        'titles' => [
            'invoice'     => 'ID Faktur #:name',
            'bill'        => 'ID Tagihan Pemasok #:name',
            'refund'      => 'ID Pengembalian Dana #:name',
            'credit-note' => 'ID Nota Kredit #:name',
        ],

        'labels' => [
            'invoice-date'          => 'Tanggal Faktur',
            'bill-date'             => 'Tanggal',
            'refund-date'           => 'Tanggal Pengembalian Dana',
            'credit-note-date'      => 'Tanggal Nota Kredit',
            'source'                => 'Sumber',
            'due-date'              => 'Tanggal Jatuh Tempo',
            'product'               => 'Produk',
            'quantity'              => 'Kuantitas',
            'unit'                  => 'Satuan',
            'unit-price'            => 'Harga Satuan',
            'subtotal'              => 'Subtotal',
            'tax'                   => 'Pajak',
            'discount'              => 'Diskon',
            'discount-percentage'   => 'Disk. %',
            'taxes'                 => 'Pajak',
            'amount'                => 'Jumlah',
            'grand-total'           => 'Total Keseluruhan',
            'payment-information'   => 'Informasi Pembayaran',
            'payment-communication' => 'Komunikasi Pembayaran',
            'account-details'       => 'pada rincian akun ini:',
        ],
    ],
];
