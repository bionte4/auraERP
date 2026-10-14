<?php

return [
    'assets' => [
        'label'   => 'Aset',
        'options' => [
            'receivable'  => 'Piutang',
            'cash'        => 'Bank dan Kas',
            'current'     => 'Aset Lancar',
            'non-current' => 'Aset Tidak Lancar',
            'prepayments' => 'Uang Muka',
            'fixed'       => 'Aset Tetap',
        ],
    ],

    'liabilities' => [
        'label'   => 'Kewajiban',
        'options' => [
            'payable'     => 'Utang',
            'credit-card' => 'Kartu Kredit',
            'current'     => 'Kewajiban Lancar',
            'non-current' => 'Liabilitas Tidak Lancar',
        ],
    ],

    'equity' => [
        'label'   => 'Ekuitas',
        'options' => [
            'equity'     => 'Ekuitas',
            'unaffected' => 'Laba Tahun Berjalan',
        ],
    ],

    'income' => [
        'label'   => 'Pendapatan',
        'options' => [
            'income' => 'Pendapatan',
            'other'  => 'Pendapatan Lain-lain',
        ],
    ],

    'expenses' => [
        'label'   => 'Beban',
        'options' => [
            'expense'      => 'Beban',
            'depreciation' => 'Penyusutan',
            'direct-cost'  => 'Beban Pokok Pendapatan',
        ],
    ],

    'off-balance' => [
        'label'   => 'Di Luar Neraca',
        'options' => [
            'off-balance' => 'Di Luar Neraca',
        ],
    ],
];
