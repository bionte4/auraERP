<?php

return [
    'columns' => [
        'invoice-date' => 'Tanggal Faktur',
        'date'         => 'Tanggal',
        'number'       => 'Nomor',
        'partner'      => 'Mitra',
        'reference'    => 'Referensi',
        'journal'      => 'Jurnal',
        'company'      => 'Perusahaan',
        'total'        => 'Total',
        'state'        => 'Status',
        'checked'      => 'Dicentang',
    ],

    'values' => [
        'yes' => 'Ya',
        'no'  => 'Tidak',
    ],

    'notification' => [
        'completed' => 'Ekspor entri jurnal Anda telah selesai dan :count baris diekspor.',
        'failed'    => ':count baris gagal diekspor.',
    ],
];
