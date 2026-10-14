<?php

return [
    'columns' => [
        'number'          => 'Nomor',
        'state'           => 'Status',
        'customer'        => 'Pelanggan',
        'bill-date'       => 'Tanggal Tagihan',
        'due-date'        => 'Tanggal Jatuh Tempo',
        'tax-excluded'    => 'Tidak Termasuk Pajak',
        'tax'             => 'Pajak',
        'total'           => 'Total',
        'amount-due'      => 'Jumlah Terutang',
        'payment-state'   => 'Status Pembayaran',
        'checked'         => 'Dicentang',
        'accounting-date' => 'Tanggal Akuntansi',
        'source-document' => 'Dokumen Sumber',
        'reference'       => 'Referensi',
        'sales-person'    => 'Tenaga Penjualan',
        'bill-currency'   => 'Mata Uang Tagihan',
    ],

    'values' => [
        'yes' => 'Ya',
        'no'  => 'Tidak',
    ],

    'notification' => [
        'completed' => 'Ekspor tagihan Anda telah selesai dan :count baris diekspor.',
        'failed'    => ':count baris gagal diekspor.',
    ],
];
