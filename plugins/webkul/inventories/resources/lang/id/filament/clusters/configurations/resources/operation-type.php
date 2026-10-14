<?php

return [
    'navigation' => [
        'title' => 'Jenis Operasi',
        'group' => 'Manajemen Gudang',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'operator-type'             => 'Tipe Operasi',
                    'operator-type-placeholder' => 'mis. Penerimaan',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Berlaku Pada',
                'description' => 'Pilih tempat di mana rute ini dapat dipilih.',

                'fields' => [
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title'  => 'Umum',

                'fields' => [
                    'operator-type'                      => 'Tipe Operasi',
                    'sequence-prefix'                    => 'Awalan Urutan',
                    'generate-shipping-labels'           => 'Buat Label Pengiriman',
                    'warehouse'                          => 'Gudang',
                    'show-reception-report'              => 'Tampilkan laporan penerimaan saat validasi',
                    'show-reception-report-hint-tooltip' => 'Jika dipilih, sistem akan secara otomatis menampilkan laporan penerimaan saat validasi, asalkan ada pergerakan yang akan dialokasikan.',
                    'company'                            => 'Perusahaan',
                    'return-type'                        => 'Jenis Retur',
                    'create-backorder'                   => 'Buat Backorder',
                    'move-type'                          => 'Jenis Perpindahan',
                    'move-type-hint-tooltip'             => 'Kecuali ditentukan oleh dokumen sumber, ini akan menjadi kebijakan pengambilan bawaan untuk jenis operasi ini.',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title'  => 'Nomor Lot/Seri',

                        'fields' => [
                            'create-new'                => 'Buat Baru',
                            'create-new-hint-tooltip'   => 'Jika dipilih, sistem akan menganggap Anda bermaksud membuat Nomor Lot/Seri baru, sehingga Anda dapat memasukkannya pada bidang teks.',
                            'use-existing'              => 'Gunakan yang Ada',
                            'use-existing-hint-tooltip' => 'Jika dipilih, Anda dapat memilih Nomor Lot/Seri atau memilih untuk tidak menetapkan apa pun. Ini memungkinkan stok dibuat tanpa lot atau tanpa pembatasan pada lot yang digunakan.',
                        ],
                    ],

                    'locations' => [
                        'title'  => 'Lokasi',

                        'fields' => [
                            'source-location'                   => 'Lokasi Sumber',
                            'source-location-hint-tooltip'      => 'Ini berfungsi sebagai lokasi sumber bawaan saat membuat operasi ini secara manual. Namun, lokasi ini dapat diubah kemudian, dan rute dapat menetapkan lokasi bawaan yang berbeda.',
                            'destination-location'              => 'Lokasi Tujuan',
                            'destination-location-hint-tooltip' => 'Ini adalah lokasi tujuan bawaan untuk operasi yang dibuat secara manual. Namun, lokasi ini dapat diubah kemudian, dan rute dapat menetapkan lokasi bawaan yang berbeda.',
                        ],
                    ],

                    'packages' => [
                        'title'  => 'Paket',

                        'fields' => [
                            'show-entire-package'              => 'Pindahkan Seluruh Paket',
                            'show-entire-package-hint-tooltip' => 'Jika dipilih, Anda dapat memindahkan seluruh paket.',
                        ],
                    ],
                ],
            ],

            'hardware' => [
                'title'  => 'Perangkat Keras',

                'fieldsets' => [
                    'print-on-validation' => [
                        'title'  => 'Cetak saat Validasi',

                        'fields' => [
                            'delivery-slip'              => 'Surat Jalan',
                            'delivery-slip-hint-tooltip' => 'Jika dipilih, sistem akan secara otomatis mencetak surat pengiriman saat pengambilan divalidasi.',

                            'return-slip'              => 'Slip Retur',
                            'return-slip-hint-tooltip' => 'Jika dipilih, sistem akan secara otomatis mencetak surat pengembalian saat pengambilan divalidasi.',

                            'product-labels'              => 'Label Produk',
                            'product-labels-hint-tooltip' => 'Jika dipilih, sistem akan secara otomatis mencetak label produk saat pengambilan divalidasi.',

                            'lots-labels'              => 'Label Lot/SN',
                            'lots-labels-hint-tooltip' => 'Jika dipilih, sistem akan secara otomatis mencetak label nomor lot/seri saat pengambilan divalidasi.',

                            'reception-report'              => 'Laporan Penerimaan',
                            'reception-report-hint-tooltip' => 'Jika dipilih, sistem akan secara otomatis mencetak laporan penerimaan saat pengambilan divalidasi dan berisi pergerakan yang ditetapkan.',

                            'reception-report-labels'              => 'Label Laporan Penerimaan',
                            'reception-report-labels-hint-tooltip' => 'Jika dipilih, sistem akan secara otomatis mencetak label laporan penerimaan saat pengambilan divalidasi.',

                            'package-content'              => 'Isi Paket',
                            'package-content-hint-tooltip' => 'Jika dipilih, sistem akan secara otomatis mencetak detail paket beserta isinya saat pengambilan divalidasi.',
                        ],
                    ],

                    'print-on-pack' => [
                        'title'  => 'Cetak pada "Masukkan ke Paket"',

                        'fields' => [
                            'package-label'              => 'Label Paket',
                            'package-label-hint-tooltip' => 'Jika dipilih, sistem akan secara otomatis mencetak label paket saat tombol "Masukkan ke Paket" digunakan.',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'warehouse'  => 'Gudang',
            'company'    => 'Perusahaan',
            'deleted-at' => 'Dihapus Pada',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'groups' => [
            'type'       => 'Tipe',
            'warehouse'  => 'Gudang',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'type'      => 'Tipe',
            'warehouse' => 'Gudang',
            'company'   => 'Perusahaan',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Jenis Operasi dipulihkan',
                    'body'  => 'Jenis operasi berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jenis Operasi dihapus',
                    'body'  => 'Jenis operasi berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Jenis Operasi dihapus permanen',
                        'body'  => 'Jenis operasi berhasil dihapus paksa.',
                    ],

                    'error' => [
                        'title' => 'Jenis operasi tidak dapat dihapus',
                        'body'  => 'Jenis operasi tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Jenis Operasi dipulihkan',
                    'body'  => 'Jenis operasi berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jenis Operasi dihapus',
                    'body'  => 'Jenis operasi berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Jenis Operasi dihapus permanen',
                        'body'  => 'Jenis operasi berhasil dihapus paksa.',
                    ],

                    'error' => [
                        'title' => 'Jenis operasi tidak dapat dihapus',
                        'body'  => 'Jenis operasi tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'empty-actions' => [
            'create' => [
                'label' => 'Buat Jenis Operasi',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'name' => 'Nama',
                ],
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

        'tabs' => [
            'general' => [
                'title' => 'Umum',

                'entries' => [
                    'type'                       => 'Tipe Operasi',
                    'sequence_code'              => 'Kode Urutan',
                    'print_label'                => 'Cetak Label',
                    'warehouse'                  => 'Gudang',
                    'reservation_method'         => 'Metode Reservasi',
                    'auto_show_reception_report' => 'Tampilkan Otomatis Laporan Penerimaan',
                    'company'                    => 'Perusahaan',
                    'return_operation_type'      => 'Jenis Operasi Pengembalian',
                    'create_backorder'           => 'Buat Backorder',
                    'move_type'                  => 'Jenis Perpindahan',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title' => 'Lot',

                        'entries' => [
                            'use_create_lots'   => 'Gunakan Pembuatan Lot',
                            'use_existing_lots' => 'Gunakan Lot yang Ada',
                        ],
                    ],

                    'locations' => [
                        'title' => 'Lokasi',

                        'entries' => [
                            'source_location'      => 'Lokasi Sumber',
                            'destination_location' => 'Lokasi Tujuan',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title' => 'Perangkat Keras',

                'fieldsets' => [
                    'print_on_validation' => [
                        'title' => 'Cetak saat Validasi',

                        'entries' => [
                            'auto_print_delivery_slip'           => 'Cetak Otomatis Slip Pengiriman',
                            'auto_print_return_slip'             => 'Cetak Slip Retur Otomatis',
                            'auto_print_product_labels'          => 'Cetak Otomatis Label Produk',
                            'auto_print_lot_labels'              => 'Cetak Label Lot Otomatis',
                            'auto_print_reception_report'        => 'Cetak Otomatis Laporan Penerimaan',
                            'auto_print_reception_report_labels' => 'Cetak Otomatis Label Laporan Penerimaan',
                            'auto_print_packages'                => 'Cetak Paket Otomatis',
                        ],
                    ],

                    'print_on_pack' => [
                        'title' => 'Cetak pada Kemasan',

                        'entries' => [
                            'auto_print_package_label' => 'Cetak Otomatis Label Paket',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
