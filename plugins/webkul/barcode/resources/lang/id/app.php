<?php

return [
    'title' => 'Barcode',

    'navigation' => [
        'back'        => 'Kembali',
        'home'        => 'Operasi',
        'search'      => 'Cari...',
        'label'       => 'Navigasi',
        'open'        => 'Buka navigasi',
        'coming-soon' => 'Segera hadir',
    ],

    'auth' => [
        'login-title'       => 'Masuk Barcode',
        'login-heading'     => 'Masuk ke Barcode',
        'login-subheading'  => 'Lanjutkan ke aplikasi operasi barcode.',
    ],

    'filament' => [
        'navigation' => [
            'group' => 'Barcode',
            'label' => 'Aplikasi Barcode',
        ],
    ],

    'dashboard' => [
        'operations' => 'Operasi',
        'empty'      => 'Tidak ada operasi yang tersedia.',
    ],

    'operation-search' => [
        'placeholder'    => 'Pindai atau masukkan kode batang operasi...',
        'open'           => 'Buka',
        'not-found'      => 'Tidak ada operasi aktif yang ditemukan untuk barcode ini.',
        'multiple-found' => ':count operasi yang cocok ditemukan.',
    ],

    'transfers' => [
        'title' => 'Transfer',
        'empty' => 'Tidak ada transfer yang ditemukan.',
    ],

    'adjustments' => [
        'title'             => 'Penyesuaian Persediaan',
        'subtitle'          => 'Hitung stok berdasarkan lokasi, produk, atau lot',
        'search'            => 'Pindai atau cari berdasarkan lokasi, produk, lot, serial...',
        'empty'             => 'Tidak ada kuantitas persediaan yang ditemukan.',
        'location-scanned'  => 'Memindai lokasi :location. Pindai lebih banyak produk di sini atau pindai lokasi lain.',
        'location-cleared'  => 'Filter penyesuaian persediaan telah dikosongkan.',
        'product-not-found' => 'Produk ini tidak tersedia pada pilihan persediaan saat ini.',
        'lot-not-found'     => 'Lot atau seri ini tidak tersedia pada pilihan persediaan saat ini.',
        'multiple-found'    => ':count kuantitas persediaan yang cocok ditemukan.',
        'count-saved'       => 'Penghitungan persediaan disimpan.',
        'count-applied'     => 'Penyesuaian persediaan diterapkan.',
        'count-cleared'     => 'Penghitungan persediaan dibersihkan.',
        'counted'           => 'Dihitung',
        'on-hand'           => 'Di tangan',
        'location'          => 'Lokasi',
        'product'           => 'Produk',
        'lot-serial'        => 'Lot/Serial',
        'clear-filters'     => 'Bersihkan filter',
        'apply'             => 'Terapkan',
        'clear'             => 'Bersihkan',
        'editor-title'      => 'Detail penyesuaian',
        'editor-subtitle'   => 'Tinjau detail stok dan perbarui kuantitas yang dihitung.',
        'editor-image'      => 'Gambar kuantitas persediaan',
        'edit-tooltip'      => 'Ubah kuantitas persediaan',
    ],

    'operation' => [
        'scan'                 => 'Pindai produk, lot, paket, kemasan, atau transfer',
        'manual-scan'          => 'Pindai atau cari berdasarkan produk, referensi, barcode...',
        'search'               => 'Cari produk, referensi, barcode...',
        'moves'                => 'Perpindahan',
        'source'               => 'Dari',
        'available'            => 'Tersedia',
        'discard'              => 'Buang',
        'confirm'              => 'Konfirmasi',
        'counted'              => 'Dihitung',
        'lot-serial'           => 'Nomor Lot/Seri',
        'stock-title'          => 'Kuantitas dalam Stok',
        'empty-moves'          => 'Tidak ada pergerakan yang ditemukan.',
        'details-title'        => 'Detail pergerakan',
        'settings-title'       => 'Pengaturan pergerakan',
        'pick-from'            => 'Ambil Dari',
        'destination-location' => 'Lokasi Tujuan',
        'destination-package'  => 'Paket Tujuan',
        'select-package'       => 'Pilih paket',
        'stock-subtitle'       => 'Pilih lokasi lain untuk mengambil produk',
        'no-stock-locations'   => 'Tidak ada lokasi stok yang ditemukan.',
        'camera-unavailable'   => 'Kamera tidak tersedia',
        'submit-scan'          => 'Kirim pindaian',
        'image-alt'            => 'Gambar baris pergerakan',
        'edit-tooltip'         => 'Ubah baris pergerakan',
    ],

    'scan' => [
        'empty'                    => 'Masukkan atau pindai kode batang.',
        'not-found'                => 'Tidak ada kode batang yang cocok.',
        'operation-matched'        => 'Transfer dicocokkan.',
        'product-not-on-operation' => 'Produk ini bukan bagian dari operasi.',
        'package-matched'          => 'Paket cocok.',
        'move-located'             => 'Pergerakan ditemukan. Masukkan kuantitas yang dihitung.',
        'move-updated'             => 'Kuantitas perpindahan diperbarui.',
        'move-counted'             => 'Perpindahan ditandai sudah dihitung.',
    ],

    'actions' => [
        'confirm'                  => 'Konfirmasi',
        'confirm-prompt'           => 'Apakah Anda yakin ingin',
        'cancel'                   => 'Batal',
        'check-availability'       => 'Periksa Ketersediaan',
        'validate'                 => 'Validasi',
        'return'                   => 'Retur',
        'stay-on-transfer'         => 'Buang',
        'no-backorder'             => 'Tanpa Pesanan Tertunda',
        'backorder-title'          => 'Transfer Belum Selesai',
        'backorder-prompt'         => 'Jika Anda memvalidasi sekarang, produk yang tersisa akan ditambahkan ke pesanan susulan.',
        'backorder-col-product'    => 'Produk',
        'backorder-col-done-todo'  => 'Selesai / Akan Dikerjakan',
        'backorder-col-backorder'  => 'Pesanan Susulan',
        'completed'                => 'Tindakan selesai.',
        'unsupported'              => 'Tindakan kode batang tidak didukung.',
        'no-moves'                 => 'Operasi ini tidak memiliki perpindahan.',
        'no-return-quantities'     => 'Tidak ada kuantitas untuk dikembalikan.',
    ],
];
