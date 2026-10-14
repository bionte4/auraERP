<?php

return [
    'inventory-manager' => [
        'check-availability' => [
            'no-moves' => 'Tidak ada yang perlu diperiksa ketersediaannya.',
        ],

        'cancel-move' => [
            'already-done' => 'Anda tidak dapat membatalkan pergerakan stok yang telah diatur ke \'Selesai\'. Buat pengembalian untuk membalikkan pergerakan yang telah terjadi.',
        ],

        'unreserve-move' => [
            'already-done' => "Anda tidak dapat membatalkan reservasi pergerakan stok yang telah diatur ke 'Selesai'.",
        ],

        'validate' => [
            'quantity-rounding-mismatch' => 'Kuantitas selesai untuk produk ":product" tidak sesuai dengan presisi pembulatan yang ditentukan pada satuan ukur ":unit". Silakan ubah kuantitas selesai atau presisi pembulatan satuan ukur Anda.',
            'no-negative-quantities'     => 'Kuantitas negatif tidak diizinkan',
            'missing-lot-serial-number'  => 'Anda perlu menyediakan Nomor Lot/Seri untuk produk:\\n:products',
        ],

        'run-procurement' => [
            'no-rule-found'      => 'Tidak ada aturan yang ditemukan untuk mengisi ulang ":product" di ":location".\\nVerifikasi konfigurasi rute pada produk.',
            'no-source-location' => 'Tidak ada lokasi sumber yang ditentukan pada aturan stok: :name!',
            'no-vendor-price'    => 'Tidak ada harga pemasok yang cocok untuk membuat pesanan pembelian bagi produk :product (pemasok tidak ditentukan, kuantitas minimum belum tercapai, tanggal tidak valid, ...). Buka formulir produk dan lengkapi daftar pemasok.',
        ],

        'return' => [
            'origin' => 'Pengembalian :operation_name',
        ],
    ],

    'move-line' => [
        'negative-quantity-not-allowed' => 'Mencadangkan kuantitas negatif tidak diizinkan.',
    ],

    'product-quantity' => [
        'quantity-not-set'                 => 'Kuantitas atau kuantitas cadangan harus diisi.',
        'removal-strategy-not-implemented' => 'Strategi pengeluaran :strategy belum diterapkan.',
        'unreserve-more-than-stock'        => 'Tidak dapat membatalkan reservasi produk :name lebih banyak daripada yang Anda miliki dalam stok.',
    ],

    'product' => [
        'endless-loop-rule' => 'Konfigurasi aturan tidak valid. Aturan berikut menyebabkan perulangan tanpa akhir: :name',
    ],

    'move' => [
        'quantity-rounding-mismatch' => 'Kuantitas selesai untuk produk :product tidak sesuai dengan presisi pembulatan yang ditentukan pada satuan ukur :unit. Silakan ubah kuantitas selesai atau presisi pembulatan satuan ukur Anda.',
        'split-done-or-cancel'       => 'Anda tidak dapat membagi pergerakan stok yang telah diatur ke \'Selesai\' atau \'Batal\'.',
        'split-draft'                => 'Anda tidak dapat membagi pergerakan draf. Pergerakan tersebut harus dikonfirmasi terlebih dahulu.',
        'serial-already-assigned'    => 'Nomor seri sudah ditetapkan pada Produk: :product, Nomor Seri: :serial_number',

        'cross-company' => [
            'title' => 'Transfer lintas perusahaan tidak diizinkan',
            'body'  => 'Transfer tidak dapat memindahkan stok secara langsung antar lokasi yang dimiliki perusahaan berbeda (:source dan :destination). Transfer antarperusahaan belum didukung.',
        ],
    ],

    'rule' => [
        'delay-on'     => 'Penundaan pada :name',
        'days'         => '+ :days hari',
        'time-horizon' => 'Jangka Waktu',
    ],
];
