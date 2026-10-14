<?php

return [
    'form' => [
        'fieldsets' => [
            'account-properties' => [
                'label' => 'Properti Akun',

                'fields' => [
                    'income-account'                    => 'Akun Pendapatan',
                    'income-account-hint-tooltip'       => 'Akun ini akan digunakan saat memvalidasi faktur pelanggan.',
                    'expense-account'                   => 'Akun Beban',
                    'expense-account-hint-tooltip'      => 'Beban dicatat saat tagihan pemasok divalidasi, kecuali pada akuntansi Anglo-Saxon dengan penilaian persediaan perpetual, di mana beban diakui sebagai Harga Pokok Penjualan saat faktur pelanggan divalidasi.',
                    'down-payment-account'              => 'Akun Uang Muka',
                    'down-payment-account-hint-tooltip' => 'Pilih akun tempat uang muka dari kategori ini akan dicatat.',
                ],
            ],
        ],
    ],
];
