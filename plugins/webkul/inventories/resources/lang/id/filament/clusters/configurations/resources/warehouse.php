<?php

return [
    'navigation' => [
        'title' => 'Gudang',
        'group' => 'Manajemen Gudang',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',
                'fields' => [
                    'name'                    => 'Nama',
                    'name-placeholder'        => 'mis. Gudang Pusat',
                    'code'                    => 'Nama Singkat',
                    'code-placeholder'        => 'mis. CW',
                    'code-hint-tooltip'       => 'Nama singkat berfungsi sebagai pengenal gudang.',
                    'company'                 => 'Perusahaan',
                    'multi-warehouse-warning' => 'Membuat gudang baru akan secara otomatis mengaktifkan pengaturan Lokasi Penyimpanan.',
                    'address'                 => 'Alamat',
                ],
            ],

            'settings' => [
                'title'  => 'Pengaturan',

                'fields' => [
                    'shipment-management'              => 'Manajemen Pengiriman',
                    'incoming-shipments'               => 'Pengiriman Masuk',
                    'incoming-shipments-hint-tooltip'  => 'Rute masuk bawaan yang diikuti',
                    'outgoing-shipments'               => 'Pengiriman Keluar',
                    'outgoing-shipments-hint-tooltip'  => 'Rute keluar bawaan yang diikuti',
                    'manufacture'                      => 'Manufaktur',
                    'manufacture-hint-tooltip'         => 'Rute manufaktur bawaan yang diikuti',
                    'resupply-management'              => 'Manajemen Pasokan Ulang',
                    'resupply-management-hint-tooltip' => 'Rute akan dibuat secara otomatis untuk memasok ulang gudang ini dari gudang yang dipilih.',
                    'resupply-from'                    => 'Pasok Ulang Dari',
                ],
            ],

            'additional' => [
                'title'  => 'Informasi Tambahan',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'code'       => 'Nama Singkat',
            'company'    => 'Perusahaan',
            'address'    => 'Alamat',
            'deleted-at' => 'dihapus Pada',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'groups' => [
            'address'       => 'Alamat',
            'company'       => 'Perusahaan',
            'created-at'    => 'Dibuat Pada',
            'updated-at'    => 'Diperbarui Pada',
        ],

        'filters' => [
            'company' => 'Perusahaan',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Gudang dipulihkan',
                    'body'  => 'Gudang berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Gudang dihapus',
                    'body'  => 'Gudang berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Gudang dihapus permanen',
                        'body'  => 'Gudang berhasil dihapus paksa.',
                    ],

                    'error' => [
                        'title' => 'Gudang tidak dapat dihapus',
                        'body'  => 'Gudang tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Gudang dipulihkan',
                    'body'  => 'Gudang berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Gudang dihapus',
                    'body'  => 'Gudang berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Gudang dihapus permanen',
                        'body'  => 'Gudang berhasil dihapus paksa.',
                    ],

                    'error' => [
                        'title' => 'Gudang tidak dapat dihapus',
                        'body'  => 'Gudang tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'name'    => 'Nama Gudang',
                    'code'    => 'Kode Gudang',
                    'company' => 'Perusahaan',
                    'address' => 'Alamat',
                ],
            ],

            'settings' => [
                'title' => 'Pengaturan',

                'entries' => [
                    'shipment-management' => 'Manajemen Pengiriman',
                    'incoming-shipments'  => 'Pengiriman Masuk',
                    'outgoing-shipments'  => 'Pengiriman Keluar',
                    'resupply-management' => 'Manajemen Pasokan Ulang',
                    'resupply-from'       => 'Pasok Ulang Dari',
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
    ],
];
