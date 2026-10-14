<?php

return [
    'title' => 'Pengguna',

    'navigation' => [
        'title' => 'Pengguna',
    ],

    'global-search' => [
        'email' => 'Email',
    ],

    'form' => [
        'validation' => [
            'cannot-remove-last-admin'   => 'Tidak dapat menghapus peran admin dari pengguna admin terakhir.',
            'first-user-must-be-admin'   => 'Pengguna pertama dalam sistem harus diberi peran admin.',
        ],

        'sections' => [
            'general-information' => [
                'title'  => 'Informasi Umum',
                'fields' => [
                    'name'                  => 'Nama',
                    'email'                 => 'Email',
                    'password'              => 'Kata Sandi',
                    'password-confirmation' => 'Konfirmasi Kata Sandi',
                ],
            ],

            'permissions' => [
                'title'  => 'Izin',
                'fields' => [
                    'roles'                                    => 'Peran',
                    'permissions'                              => 'Izin',
                    'resource-permission'                      => 'Izin Sumber Daya',
                    'resource-permission-self-change-disabled' => 'Anda tidak dapat mengubah izin sumber daya Anda sendiri. Minta administrator lain untuk memperbaruinya.',
                    'teams'                                    => 'Tim',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'  => 'Bahasa & Status',
                'fields' => [
                    'language' => 'Bahasa Pilihan',
                    'status'   => 'Status',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Multi Perusahaan',
                'allowed-companies'           => 'Perusahaan yang Diizinkan',
                'default-company'             => 'Perusahaan Bawaan',
                'default-company-not-allowed' => 'Perusahaan bawaan harus merupakan salah satu perusahaan yang diizinkan.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'avatar'              => 'Avatar',
            'name'                => 'Nama',
            'email'               => 'Email',
            'teams'               => 'Tim',
            'role'                => 'Peran',
            'resource-permission' => 'Izin Sumber Daya',
            'default-company'     => 'Perusahaan Bawaan',
            'allowed-company'     => 'Perusahaan yang Diizinkan',
            'created-by'          => 'Dibuat Oleh',
            'created-at'          => 'Dibuat Pada',
            'updated-at'          => 'Diperbarui Pada',
        ],

        'filters' => [
            'resource-permission' => 'Izin Sumber Daya',
            'teams'               => 'Tim',
            'roles'               => 'Peran',
            'default-company'     => 'Perusahaan Bawaan',
            'allowed-companies'   => 'Perusahaan yang Diizinkan',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Pengguna diubah',
                    'body'  => 'Pengguna berhasil diubah.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Pengguna dihapus',
                    'body'  => 'Pengguna berhasil dihapus.',
                    'error' => [
                        'title' => 'Pengguna Tidak Dapat Dihapus',
                        'body'  => 'Ini adalah pengguna bawaan atau Anda tidak dapat menghapus diri sendiri.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Pengguna dipulihkan',
                    'body'  => 'Pengguna berhasil dipulihkan.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Pengguna dipulihkan',
                    'body'  => 'Pengguna berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Pengguna dihapus',
                    'body'  => 'Pengguna berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Pengguna dihapus paksa',
                    'body'  => 'Pengguna berhasil dihapus secara paksa.',
                    'error' => [
                        'title' => 'Pengguna tidak dapat dihapus',
                        'body'  => 'Pengguna tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Pengguna dibuat',
                    'body'  => 'Pengguna berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title'   => 'Informasi Umum',
                'entries' => [
                    'name'                  => 'Nama',
                    'email'                 => 'Email',
                    'password'              => 'Kata Sandi',
                    'password-confirmation' => 'Konfirmasi Kata Sandi',
                ],
            ],

            'permissions' => [
                'title'   => 'Izin',
                'entries' => [
                    'roles'               => 'Peran',
                    'permissions'         => 'Izin',
                    'resource-permission' => 'Izin Sumber Daya',
                    'teams'               => 'Tim',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'   => 'Bahasa & Status',
                'entries' => [
                    'language' => 'Bahasa Pilihan',
                    'status'   => 'Status',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Multi Perusahaan',
                'allowed-companies'           => 'Perusahaan yang Diizinkan',
                'default-company'             => 'Perusahaan Bawaan',
                'default-company-not-allowed' => 'Perusahaan bawaan harus merupakan salah satu perusahaan yang diizinkan.',
            ],
        ],
    ],
];
