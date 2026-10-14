<?php

return [
    'title' => 'Jenis Keahlian',

    'navigation' => [
        'title' => 'Jenis Keahlian',
        'group' => 'Karyawan',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'             => 'Nama',
                'name-placeholder' => 'Masukkan nama jenis keterampilan',
                'color'            => 'Warna',
                'status'           => 'Status',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Jenis Keahlian',
            'status'     => 'Status',
            'color'      => 'Warna',
            'skills'     => 'Keahlian',
            'levels'     => 'Tingkat',
            'created-by' => 'Dibuat Oleh',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'skill-levels' => 'Tingkat Keterampilan',
            'skills'       => 'Keahlian',
            'created-by'   => 'Dibuat Oleh',
            'status'       => 'Status',
            'updated-at'   => 'Diperbarui Pada',
            'created-at'   => 'Dibuat Pada',
        ],

        'groups' => [
            'name'       => 'Jenis Keahlian',
            'color'      => 'Warna',
            'status'     => 'Status',
            'created-by' => 'Dibuat Oleh',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Jenis Keterampilan dipulihkan',
                    'body'  => 'Jenis keahlian berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jenis Keterampilan dihapus',
                    'body'  => 'Jenis keahlian berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Jenis Keterampilan dipulihkan',
                    'body'  => 'Jenis keahlian berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jenis Keterampilan dihapus',
                    'body'  => 'Jenis keahlian berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Jenis Keterampilan dihapus permanen',
                    'body'  => 'Jenis keterampilan berhasil dihapus paksa.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Jenis Keahlian',
                    'body'  => 'Jenis keahlian berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'   => 'Jenis Keahlian',
                'color'  => 'Warna',
                'status' => 'Status',
            ],
        ],
    ],
];
