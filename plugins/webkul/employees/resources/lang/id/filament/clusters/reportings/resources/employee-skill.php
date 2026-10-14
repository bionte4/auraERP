<?php

return [
    'title' => 'Keahlian',

    'navigation' => [
        'title' => 'Keahlian',
    ],

    'form' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Detail Keterampilan',

                'fields' => [
                    'employee'       => 'Karyawan',
                    'skill'          => 'Keahlian',
                    'skill-level'    => 'Tingkat',
                    'skill-type'     => 'Jenis Keahlian',
                ],
            ],
            'addition-information' => [
                'title' => 'Informasi Tambahan',

                'fields' => [
                    'created-by' => 'Dibuat Oleh',
                    'updated-by' => 'Diperbarui Oleh',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'employee'        => 'Karyawan',
            'skill'           => 'Keahlian',
            'skill-level'     => 'Tingkat',
            'skill-type'      => 'Jenis Keahlian',
            'user'            => 'Pengguna',
            'proficiency'     => 'Kemahiran',
            'created-by'      => 'Dibuat Oleh',
            'created-at'      => 'Dibuat Pada',
        ],

        'filters' => [
            'employee'        => 'Karyawan',
            'skill'           => 'Keahlian',
            'skill-level'     => 'Tingkat',
            'skill-type'      => 'Jenis Keahlian',
            'user'            => 'Pengguna',
            'created-by'      => 'Dibuat Oleh',
            'created-at'      => 'Dibuat Pada',
            'updated-at'      => 'Diperbarui Pada',
        ],

        'groups' => [
            'employee'   => 'Karyawan',
            'skill-type' => 'Jenis Keahlian',
        ],
    ],

    'infolist' => [
        'sections' => [
            'skill-details' => [
                'title' => 'Detail Keterampilan',

                'entries' => [
                    'employee'        => 'Karyawan',
                    'skill'           => 'Keahlian',
                    'skill-level'     => 'Tingkat',
                    'skill-type'      => 'Jenis Keahlian',
                ],
            ],

            'additional-information' => [
                'title' => 'Informasi Tambahan',

                'entries' => [
                    'created-by' => 'Dibuat Oleh',
                    'updated-by' => 'Diperbarui Oleh',
                ],
            ],
        ],
    ],
];
