<?php

return [
    'navigation' => [
        'title' => 'Kontrol Anggaran Proyek',
    ],

    'title'       => 'Kontrol Anggaran Proyek',
    'description' => 'Nilai kontrak vs penagihan dan serapan anggaran operasional per proyek. Realisasi berasal dari invoice dan bill vendor ter-posting yang dikaitkan ke proyek; kaitkan dokumen lewat field Proyek pada form invoice/bill.',

    'summary' => [
        'contract'    => 'Total nilai kontrak',
        'invoiced'    => 'Total terinvoice',
        'unpaid'      => 'Total belum terbayar',
        'vendor-cost' => 'Total biaya vendor',
        'attention'   => 'Proyek perlu perhatian',
    ],

    'status' => [
        'all'       => 'Semua',
        'ok'        => 'Aman',
        'warning'   => 'Waspada ≥80%',
        'over'      => 'Over budget',
        'no_budget' => 'Belum ada anggaran',
    ],

    'search-placeholder' => 'Cari proyek atau customer…',

    'table' => [
        'project'     => 'Proyek',
        'customer'    => 'Customer',
        'contract'    => 'Nilai Kontrak',
        'invoiced'    => 'Terinvoice',
        'unpaid'      => 'Belum terbayar',
        'budget'      => 'Anggaran Operasional',
        'vendor-cost' => 'Biaya Vendor (realisasi)',
        'absorption'  => 'Serapan',
        'hours'       => 'Jam Timesheet',
        'empty'       => 'Tidak ada proyek yang cocok dengan filter saat ini.',
    ],

    'note' => 'Serapan = bill vendor ter-posting yang dikaitkan ke proyek ÷ anggaran operasional pada proyek. Dokumen yang belum dikaitkan ke proyek tidak ikut terhitung — kaitkan dari form invoice/bill.',

    'actions' => [
        'export' => 'Ekspor Excel',
    ],
];
