<?php

return [
    'title' => 'Departemen',

    'navigation' => [
        'title' => 'Departemen',
        'group' => 'Karyawan',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Informasi Umum',

                'fields' => [
                    'name'                => 'Tipe Aktivitas',
                    'name-tooltip'        => 'Masukkan nama resmi jenis aktivitas',
                    'action'              => 'Tindakan',
                    'default-user'        => 'Pengguna Bawaan',
                    'summary'             => 'Ringkasan',
                    'note'                => 'Catatan',
                ],
            ],

            'delay-information' => [
                'title' => 'Informasi Keterlambatan',

                'fields' => [
                    'delay-count'            => 'Jumlah Penundaan',
                    'delay-unit'             => 'Satuan Penundaan',
                    'delay-form'             => 'Bentuk Penundaan',
                    'delay-form-helper-text' => 'Sumber perhitungan keterlambatan',
                ],
            ],

            'advanced-information' => [
                'title' => 'Informasi Lanjutan',

                'fields' => [
                    'icon'                => 'Ikon',
                    'decoration-type'     => 'Jenis Dekorasi',
                    'chaining-type'       => 'Tipe Perangkaian',
                    'suggest'             => 'Sarankan',
                    'trigger'             => 'Pemicu',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status & Konfigurasi',

                'fields' => [
                    'status'               => 'Status',
                    'keep-done-activities' => 'Pertahankan Aktivitas Selesai',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Tipe Aktivitas',
            'summary'    => 'Ringkasan',
            'planned-in' => 'Direncanakan Dalam',
            'type'       => 'Tipe',
            'action'     => 'Tindakan',
            'status'     => 'Status',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'groups' => [
            'name'             => 'Nama',
            'action-category'  => 'Kategori Tindakan',
            'status'           => 'Status',
            'delay-count'      => 'Jumlah Penundaan',
            'delay-unit'       => 'Satuan Penundaan',
            'delay-source'     => 'Sumber Penundaan',
            'associated-model' => 'Model Terkait',
            'chaining-type'    => 'Tipe Perangkaian',
            'decoration-type'  => 'Jenis Dekorasi',
            'default-user'     => 'Pengguna Bawaan',
            'creation-date'    => 'Tanggal Pembuatan',
            'last-update'      => 'Pembaruan Terakhir',
        ],

        'filters' => [
            'action'    => 'Tindakan',
            'status'    => 'Status',
            'has-delay' => 'Ada Keterlambatan',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Jenis aktivitas dipulihkan',
                    'body'  => 'Jenis aktivitas berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jenis aktivitas dihapus',
                    'body'  => 'Jenis aktivitas berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Jenis aktivitas dihapus permanen',
                        'body'  => 'Jenis aktivitas berhasil dihapus paksa.',
                    ],
                    'error' => [
                        'title' => 'Jenis aktivitas  tidak dapat dihapus',
                        'body'  => 'Jenis aktivitas tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Jenis aktivitas dipulihkan',
                    'body'  => 'Jenis aktivitas berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jenis aktivitas dihapus',
                    'body'  => 'Jenis aktivitas berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Jenis aktivitas dihapus permanen',
                    'body'  => 'Jenis aktivitas berhasil dihapus paksa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'name'                => 'Tipe Aktivitas',
                    'name-tooltip'        => 'Masukkan nama resmi jenis aktivitas',
                    'action'              => 'Tindakan',
                    'default-user'        => 'Pengguna Bawaan',
                    'plugin'              => 'Plugin',
                    'summary'             => 'Ringkasan',
                    'note'                => 'Catatan',
                ],
            ],

            'delay-information' => [
                'title' => 'Informasi Keterlambatan',

                'entries' => [
                    'delay-count'            => 'Jumlah Penundaan',
                    'delay-unit'             => 'Satuan Penundaan',
                    'delay-form'             => 'Bentuk Penundaan',
                    'delay-form-helper-text' => 'Sumber perhitungan keterlambatan',
                ],
            ],

            'advanced-information' => [
                'title' => 'Informasi Lanjutan',

                'entries' => [
                    'icon'                => 'Ikon',
                    'decoration-type'     => 'Jenis Dekorasi',
                    'chaining-type'       => 'Tipe Perangkaian',
                    'suggest'             => 'Sarankan',
                    'trigger'             => 'Pemicu',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Status & Konfigurasi',

                'entries' => [
                    'status'               => 'Status',
                    'keep-done-activities' => 'Pertahankan Aktivitas Selesai',
                ],
            ],
        ],
    ],
];
