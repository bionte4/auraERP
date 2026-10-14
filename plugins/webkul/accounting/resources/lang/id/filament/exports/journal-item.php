<?php

return [
    'columns' => [
        'number'           => 'Nomor',
        'date'             => 'Tanggal',
        'account'          => 'Akun',
        'partner'          => 'Mitra',
        'label'            => 'Label',
        'reference'        => 'Referensi',
        'journal'          => 'Jurnal',
        'debit'            => 'Debit',
        'credit'           => 'Kredit',
        'balance'          => 'Saldo',
        'currency'         => 'Mata Uang',
        'company'          => 'Perusahaan',
        'status'           => 'Status',
        'amount-currency'  => 'Jumlah Mata Uang',
        'amount-residual'  => 'Jumlah Sisa',
        'reconciled'       => 'Terekonsiliasi',
        'due-date'         => 'Tanggal Jatuh Tempo',
    ],

    'values' => [
        'yes' => 'Ya',
        'no'  => 'Tidak',
    ],

    'notification' => [
        'completed' => 'Ekspor item jurnal Anda telah selesai dan :count baris diekspor.',
        'failed'    => ':count baris gagal diekspor.',
    ],
];
