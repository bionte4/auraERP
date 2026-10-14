<?php

return [
    'title' => 'Kandidat',

    'navigation' => [
        'title' => 'Kandidat',
    ],

    'global-search' => [
        'email-from' => 'Email Dari',
        'phone'      => 'Telepon',
        'company'    => 'Perusahaan',
        'degree'     => 'Gelar',
    ],

    'form' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Informasi Dasar',

                'fields' => [
                    'full-name' => 'Nama Lengkap',
                    'email'     => 'Alamat Email',
                    'phone'     => 'Nomor Telepon',
                    'linkedin'  => 'Profil LinkedIn',
                    'contact'   => 'Kontak',
                ],
            ],

            'additional-details' => [
                'title' => 'Detail Tambahan',

                'fields' => [
                    'company'           => 'Perusahaan',
                    'degree'            => 'Gelar',
                    'tags'              => 'Tag',
                    'manager'           => 'Manajer',
                    'availability-date' => 'Tanggal Ketersediaan',

                    'priority-options' => [
                        'low'    => 'Rendah',
                        'medium' => 'Sedang',
                        'high'   => 'Tinggi',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Status',

                'fields' => [
                    'active'     => 'Aktif',
                    'evaluation' => 'Evaluasi',
                ],
            ],

            'communication' => [
                'title' => 'Komunikasi',

                'fields' => [
                    'cc-email'      => 'Email CC',
                    'email-bounced' => 'Email Terpental',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama Lengkap',
            'tags'       => 'Tag',
            'evaluation' => 'Evaluasi',
        ],

        'filters' => [
            'company'      => 'Perusahaan',
            'partner-name' => 'Kontak',
            'degree'       => 'Gelar',
            'manager-name' => 'Manajer',
        ],

        'groups' => [
            'manager-name' => 'Manajer',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kandidat Dihapus',
                    'body'  => 'Kandidat berhasil dihapus.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Kandidat Dibuat',
                    'body'  => 'Kandidat berhasil dibuat.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kandidat Dihapus',
                    'body'  => 'Kandidat berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => [
                'title' => 'Informasi Dasar',

                'entries' => [
                    'full-name' => 'Nama Lengkap',
                    'email'     => 'Alamat Email',
                    'phone'     => 'Nomor Telepon',
                    'linkedin'  => 'Profil LinkedIn',
                    'contact'   => 'Kontak',
                ],
            ],

            'additional-details' => [
                'title' => 'Detail Tambahan',

                'entries' => [
                    'company'           => 'Perusahaan',
                    'degree'            => 'Gelar',
                    'tags'              => 'Tag',
                    'manager'           => 'Manajer',
                    'availability-date' => 'Tanggal Ketersediaan',

                    'priority-options' => [
                        'low'    => 'Rendah',
                        'medium' => 'Sedang',
                        'high'   => 'Tinggi',
                    ],
                ],
            ],

            'status-and-evaluation' => [
                'title' => 'Status',

                'entries' => [
                    'active'     => 'Aktif',
                    'evaluation' => 'Evaluasi',
                ],
            ],

            'communication' => [
                'title' => 'Komunikasi',

                'entries' => [
                    'cc-email'      => 'Email CC',
                    'email-bounced' => 'Email Terpental',
                ],
            ],
        ],
    ],
];
