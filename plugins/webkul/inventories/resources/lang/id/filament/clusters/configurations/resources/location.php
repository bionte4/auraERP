<?php

return [
    'navigation' => [
        'title' => 'Lokasi',
        'group' => 'Manajemen Gudang',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',

                'fields' => [
                    'location'                     => 'Lokasi',
                    'location-placeholder'         => 'mis. Stok Cadangan',
                    'parent-location'              => 'Lokasi Induk',
                    'parent-location-hint-tooltip' => 'Lokasi utama yang mencakup lokasi ini. Misalnya, \'Zona Pengiriman\' merupakan bagian dari lokasi induk \'Gerbang 1\'.',
                    'external-notes'               => 'Catatan Eksternal',
                ],
            ],

            'settings' => [
                'title'  => 'Pengaturan',

                'fields' => [
                    'location-type'                 => 'Tipe Lokasi',
                    'company'                       => 'Perusahaan',
                    'storage-category'              => 'Kategori Penyimpanan',
                    'is-scrap'                      => 'Apakah Lokasi Barang Afkir?',
                    'is-scrap-hint-tooltip'         => 'Pilih kotak centang ini untuk menetapkan lokasi ini sebagai tempat penyimpanan barang afkir atau rusak.',
                    'is-dock'                       => 'Apakah Lokasi Dermaga?',
                    'is-dock-hint-tooltip'          => 'Pilih kotak centang ini untuk menetapkan lokasi ini sebagai tempat penyimpanan barang yang siap dikirim.',
                    'is-replenish'                  => 'Apakah Lokasi Pengisian Ulang?',
                    'is-replenish-hint-tooltip'     => 'Aktifkan fungsi ini untuk mengambil semua kuantitas yang diperlukan bagi pengisian ulang di lokasi ini.',
                    'logistics'                     => 'Logistik',
                    'removal-strategy'              => 'Strategi Pengambilan',
                    'removal-strategy-hint-tooltip' => 'Menentukan metode bawaan untuk menetapkan rak, lot, dan lokasi yang tepat saat mengambil produk. Metode ini dapat diterapkan pada tingkat kategori produk, dengan cadangan ke lokasi induk jika tidak diatur di sini.',
                    'cyclic-counting'               => 'Penghitungan Siklus',
                    'inventory-frequency'           => 'Frekuensi Persediaan',
                    'last-inventory'                => 'Persediaan Terakhir',
                    'last-inventory-hint-tooltip'   => 'Tanggal inventaris terakhir di lokasi ini.',
                    'next-expected'                 => 'Perkiraan berikutnya',
                    'next-expected-hint-tooltip'    => 'Tanggal persediaan terencana berikutnya berdasarkan jadwal siklus.',
                ],
            ],

            'additional' => [
                'title'  => 'Informasi Tambahan',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'location'         => 'Lokasi',
            'type'             => 'Tipe',
            'storage-category' => 'Kategori Penyimpanan',
            'company'          => 'Perusahaan',
            'deleted-at'       => 'dihapus Pada',
            'created-at'       => 'Dibuat Pada',
            'updated-at'       => 'Diperbarui Pada',
        ],

        'groups' => [
            'warehouse'       => 'Gudang',
            'type'            => 'Tipe',
            'created-at'      => 'Dibuat Pada',
            'updated-at'      => 'Diperbarui Pada',
        ],

        'filters' => [
            'location' => 'Lokasi',
            'type'     => 'Tipe',
            'company'  => 'Perusahaan',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Lokasi diperbarui',
                    'body'  => 'Lokasi berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Lokasi dipulihkan',
                    'body'  => 'Lokasi berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lokasi dihapus',
                    'body'  => 'Lokasi berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lokasi dihapus permanen',
                        'body'  => 'Lokasi berhasil dihapus paksa.',
                    ],

                    'error' => [
                        'title' => 'Lokasi tidak dapat dihapus',
                        'body'  => 'Lokasi tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Cetak Barcode',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Lokasi dipulihkan',
                    'body'  => 'Lokasi berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Lokasi dihapus',
                    'body'  => 'Lokasi berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Lokasi dihapus permanen',
                        'body'  => 'Lokasi berhasil dihapus paksa.',
                    ],

                    'error' => [
                        'title' => 'Lokasi tidak dapat dihapus',
                        'body'  => 'Lokasi tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',

                'entries' => [
                    'location'                     => 'Lokasi',
                    'location-placeholder'         => 'mis. Stok Cadangan',
                    'parent-location'              => 'Lokasi Induk',
                    'parent-location-hint-tooltip' => 'Lokasi utama yang mencakup lokasi ini. Misalnya, \'Zona Pengiriman\' merupakan bagian dari lokasi induk \'Gerbang 1\'.',
                    'external-notes'               => 'Catatan Eksternal',
                ],
            ],

            'settings' => [
                'title'  => 'Pengaturan',

                'entries' => [
                    'location-type'                 => 'Tipe Lokasi',
                    'company'                       => 'Perusahaan',
                    'storage-category'              => 'Kategori Penyimpanan',
                    'is-scrap'                      => 'Apakah Lokasi Barang Afkir?',
                    'is-scrap-hint-tooltip'         => 'Pilih kotak centang ini untuk menetapkan lokasi ini sebagai tempat penyimpanan barang afkir atau rusak.',
                    'is-dock'                       => 'Apakah Lokasi Dermaga?',
                    'is-dock-hint-tooltip'          => 'Pilih kotak centang ini untuk menetapkan lokasi ini sebagai tempat penyimpanan barang yang siap dikirim.',
                    'is-replenish'                  => 'Apakah Lokasi Pengisian Ulang?',
                    'is-replenish-hint-tooltip'     => 'Aktifkan fungsi ini untuk mengambil semua kuantitas yang diperlukan bagi pengisian ulang di lokasi ini.',
                    'logistics'                     => 'Logistik',
                    'removal-strategy'              => 'Strategi Pengambilan',
                    'removal-strategy-hint-tooltip' => 'Menentukan metode bawaan untuk menetapkan rak, lot, dan lokasi yang tepat saat mengambil produk. Metode ini dapat diterapkan pada tingkat kategori produk, dengan cadangan ke lokasi induk jika tidak diatur di sini.',
                    'cyclic-counting'               => 'Penghitungan Siklus',
                    'inventory-frequency'           => 'Frekuensi Persediaan',
                    'last-inventory'                => 'Persediaan Terakhir',
                    'last-inventory-hint-tooltip'   => 'Tanggal inventaris terakhir di lokasi ini.',
                    'next-expected'                 => 'Perkiraan berikutnya',
                    'next-expected-hint-tooltip'    => 'Tanggal persediaan terencana berikutnya berdasarkan jadwal siklus.',
                ],
            ],

            'additional' => [
                'title'  => 'Informasi Tambahan',
            ],

            'record-information' => [
                'title' => 'Informasi Rekaman',

                'entries' => [
                    'created-by'   => 'Dibuat Oleh',
                    'created-at'   => 'Dibuat Pada',
                    'last-updated' => 'Terakhir Diperbarui',
                ],
            ],
        ],
    ],
];
