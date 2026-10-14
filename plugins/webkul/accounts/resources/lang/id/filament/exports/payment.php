<?php

return [
    'columns' => [
        'date'            => 'Tanggal',
        'name'            => 'Nama',
        'journal'         => 'Jurnal',
        'payment-method'  => 'Metode Pembayaran',
        'partner'         => 'Mitra',
        'amount-currency' => 'Jumlah Mata Uang',
        'amount'          => 'Jumlah',
        'state'           => 'Status',
        'company'         => 'Perusahaan',
    ],

    'notification' => [
        'completed' => 'Ekspor pembayaran Anda telah selesai dan :count baris diekspor.',
        'failed'    => ':count baris gagal diekspor.',
    ],
];
