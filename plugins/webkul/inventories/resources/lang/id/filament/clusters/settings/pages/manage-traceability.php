<?php

return [
    'title' => 'Kelola Ketertelusuran',

    'form' => [
        'enable-lots-serial-numbers'                             => 'Nomor Lot & Seri',
        'enable-lots-serial-numbers-helper-text'                 => 'Dapatkan ketertelusuran penuh dari pemasok hingga pelanggan',
        'configure-lots'                                         => 'Konfigurasi Lot',
        'enable-expiration-dates'                                => 'Tanggal Kedaluwarsa',
        'enable-expiration-dates-helper-text'                    => 'Tetapkan tanggal kedaluwarsa pada lot & nomor seri',
        'display-on-delivery-slips'                              => 'Tampilkan pada Slip Pengiriman',
        'display-on-delivery-slips-helper-text'                  => 'Nomor lot dan seri akan muncul pada surat pengiriman',
        'display-expiration-dates-on-delivery-slips'             => 'Tampilkan tanggal kedaluwarsa pada surat jalan',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'Tanggal kedaluwarsa akan muncul pada surat pengiriman',
        'enable-consignments'                                    => 'Konsinyasi',
        'enable-consignments-helper-text'                        => 'Tetapkan pemilik pada produk yang disimpan',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Anda memiliki produk dalam stok yang pelacakan nomor lot/serinya diaktifkan. ',
                'body'  => 'Nonaktifkan pelacakan pada semua produk terlebih dahulu sebelum menonaktifkan pengaturan ini.',
            ],
        ],
    ],
];
