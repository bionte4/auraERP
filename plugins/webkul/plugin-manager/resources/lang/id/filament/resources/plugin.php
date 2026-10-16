<?php

return [

    'title' => 'Plugin',

    'table' => [
        'version'             => 'Versi',
        'dependencies'        => 'Ketergantungan',
        'dependencies_suffix' => ' Ketergantungan',
    ],

    'status' => [
        'installed'     => 'Terpasang',
        'not_installed' => 'Belum Terpasang',
    ],

    'filters' => [
        'installation_status' => 'Status Instalasi',
        'all_plugins'         => 'Semua Plugin',
        'installed'           => 'Terpasang',
        'not_installed'       => 'Belum Terpasang',
        'active_status'       => 'Status Aktif',
        'author'              => 'Penulis',
        'webkul'              => 'Webkul',
        'third_party'         => 'Pihak Ketiga',
    ],

    'actions' => [
        'install' => [
            'title'       => 'Pasang',
            'heading'     => 'Pasang Plugin :name',
            'description' => "Apakah Anda yakin ingin memasang plugin ':name'? Tindakan ini akan menjalankan migrasi dan seeder.",
            'submit'      => 'Pasang Plugin',
        ],
        'uninstall' => [
            'title'      => 'Copot Pemasangan',
            'heading'    => 'Copot Plugin',
            'submit'     => 'Copot Plugin',
        ],
    ],

    'notifications' => [
        'installed' => [
            'title' => 'Plugin Berhasil Dipasang',
            'body'  => "Plugin ':name' telah dipasang.",
        ],
        'installed-failed' => [
            'title' => 'Instalasi Gagal',
        ],
        'uninstalled' => [
            'title' => 'Plugin Berhasil Dicopot',
            'body'  => "Plugin ':name' telah dicopot.",
        ],
        'uninstalled-failed' => [
            'title' => 'Pencopotan Gagal',
        ],
        'uninstalled-blocked' => [
            'title' => 'Tidak Dapat Mencopot Plugin',
            'body'  => "Plugin ':name' memiliki dependensi terpasang yang harus dicopot pemasangannya terlebih dahulu: :dependents.",
        ],
    ],

    'infolist' => [
        'section'  => [
            'plugin'       => ' Informasi Plugin',
            'dependencies' => 'Ketergantungan',
        ],
        'name'         => 'Nama Plugin',
        'author'       => 'Penulis',
        'version'      => 'Versi',
        'dependencies' => 'Plugin yang Diperlukan',
        'dependents'   => 'Plugin yang Bergantung pada Ini',
        'is_installed' => 'Status Instalasi',
        'license'      => 'Lisensi',
        'summary'      => 'Deskripsi',

        'dependencies-repeater' => [
            'title'        => 'Plugin yang Diperlukan',
            'name'         => 'Nama Plugin',
            'is_installed' => 'Terpasang',
            'placeholder'  => 'Tidak ada ketergantungan yang diperlukan',
        ],

        'dependents-repeater' => [
            'title'        => 'Plugin yang Bergantung pada Ini',
            'name'         => 'Nama Plugin',
            'is_installed' => 'Terpasang',
            'placeholder'  => 'Tidak ada tanggungan',
        ],

    ],

    'names' => [
        'accounting'     => 'Akuntansi',
        'accounts'       => 'Akun',
        'analytics'      => 'Analitik',
        'barcode'        => 'Barcode',
        'blogs'          => 'Blog',
        'chatter'        => 'Chatter',
        'contacts'       => 'Kontak',
        'employees'      => 'Karyawan',
        'fields'         => 'Bidang Kustom',
        'full-calendar'  => 'Kalender',
        'integrations'   => 'Integrasi',
        'inventories'    => 'Persediaan',
        'invoices'       => 'Faktur',
        'maintenance'    => 'Pemeliharaan',
        'manufacturing'  => 'Manufaktur',
        'partners'       => 'Mitra',
        'payments'       => 'Pembayaran',
        'plugin-manager' => 'Pengelola Plugin',
        'products'       => 'Produk',
        'projects'       => 'Proyek',
        'purchases'      => 'Pembelian',
        'recruitments'   => 'Rekrutmen',
        'sales'          => 'Penjualan',
        'security'       => 'Keamanan',
        'support'        => 'Dukungan',
        'table-views'    => 'Tampilan Tabel',
        'time-off'       => 'Cuti',
        'timesheets'     => 'Lembar Waktu',
        'website'        => 'Situs Web',
    ],

    'summaries' => [
        'accounting'     => 'Kelola bagan akun, jurnal, dan entri keuangan',
        'accounts'       => 'Manajemen akun inti dan pengaturan keuangan',
        'analytics'      => 'Laporan dan dasbor untuk wawasan bisnis',
        'barcode'        => 'Aplikasi operasi kode batang untuk persediaan dan manufaktur',
        'blogs'          => 'Kelola blog',
        'chatter'        => 'Log aktivitas, pesan, dan tindak lanjut pada data',
        'contacts'       => 'Manajemen kontak untuk pelanggan dan pemasok',
        'employees'      => 'Manajemen karyawan',
        'fields'         => 'Tambahkan bidang kustom ke sumber daya',
        'full-calendar'  => 'Tampilan kalender dan penjadwalan acara',
        'integrations'   => 'Pengaturan koneksi SMTP, auraAI, dan Jira beserta tombol uji',
        'inventories'    => 'Manajemen persediaan dan gudang',
        'invoices'       => 'Pembuatan dan manajemen faktur',
        'maintenance'    => 'Manajemen pemeliharaan',
        'manufacturing'  => 'Manajemen manufaktur dan produksi',
        'partners'       => 'Kelola mitra bisnis',
        'payments'       => 'Kelola pembayaran dan transaksi',
        'plugin-manager' => 'Pengelola Plugin untuk auraERP',
        'products'       => 'Katalog produk dan pengelolaan varian',
        'projects'       => 'Perencanaan dan manajemen proyek',
        'purchases'      => 'Manajemen pengadaan dan pesanan pembelian',
        'recruitments'   => 'Pelacakan pelamar dan perekrutan',
        'sales'          => 'Manajemen saluran penjualan dan peluang',
        'security'       => 'Peran, izin, dan kontrol akses',
        'support'        => 'Dukungan pelanggan dan tiket',
        'table-views'    => 'Tampilan tabel tersimpan dan dapat disesuaikan',
        'time-off'       => 'Manajemen dan pelacakan cuti',
        'timesheets'     => 'Pelacakan jam kerja karyawan',
        'website'        => 'Situs web untuk pelanggan',
    ],

];
