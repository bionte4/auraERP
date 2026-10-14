<?php

return [
    'manufacturing-manager' => [
        'unplan-order' => [
            'work-orders-already-done'    => 'Beberapa perintah kerja sudah selesai, sehingga Anda tidak dapat membatalkan perencanaan perintah manufaktur ini.\\n\\nSayang jika semua kemajuan itu terbuang, bukan?',
            'work-orders-already-started' => 'Beberapa perintah kerja sudah dimulai, sehingga Anda tidak dapat membatalkan perencanaan perintah manufaktur ini.\\n\\nSayang jika semua kemajuan itu terbuang, bukan?',
        ],
    ],

    'work-center-productivity-log' => [
        'time-tracking'                    => 'Pelacakan Waktu: :name',
        'no-performance-productivity-loss' => "Anda perlu menetapkan setidaknya satu kehilangan produktivitas yang tidak diarsipkan pada kategori 'Kinerja'. Buat dari pengaturan konfigurasi.",
    ],

    'work-center' => [
        'already-unblocked' => 'Sudah dibuka blokirnya.',
    ],

    'work-order' => [
        'unblock-work-center'        => 'Silakan buka blokir pusat kerja untuk memulai perintah kerja.',
        'already-done-or-cancelled'  => 'Anda tidak dapat memulai perintah kerja yang sudah selesai atau dibatalkan',
        'no-calendar-on-work-center' => 'Tidak ada kalender yang ditentukan pada pusat kerja :name.',
        'no-productivity-loss'       => "Anda perlu menetapkan setidaknya satu kehilangan produktivitas pada kategori 'Produktivitas'. Buat pengaturan Konfigurasi.",
        'no-performance-loss'        => "Anda perlu menetapkan setidaknya satu kehilangan produktivitas pada kategori 'Kinerja'. Buat pengaturan Konfigurasi.",
        'impossible-to-plan'         => 'Tidak dapat merencanakan perintah kerja. Silakan periksa ketersediaan pusat kerja.',
    ],

    'order' => [
        'product-in-byproducts'                    => 'Anda tidak dapat menempatkan :product sebagai produk jadi sekaligus dalam produk sampingan',
        'missing-lot-serial-number'                => 'Anda perlu menyediakan Nomor Lot/Seri untuk produk dan "mengonsumsi" produk tersebut: :missing_products',
        'serial-number-already-produced'           => 'Nomor seri ini untuk produk :product sudah diproduksi',
        'byproduct-serial-number-already-produced' => 'Nomor seri :number yang digunakan untuk produk sampingan :product sudah diproduksi',
        'component-serial-number-consumed'         => 'Nomor seri :number yang digunakan untuk komponen :component sudah dikonsumsi',
        'components-availability'                  => [
            'available'     => 'Tersedia',
            'not-available' => 'Tidak Tersedia',
            'expected'      => 'Perkiraan :date',
        ],
    ],
];
