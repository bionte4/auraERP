<?php

return [
    'navigation' => [
        'title' => 'Jabatan',
        'group' => 'Jabatan',
    ],

    'global-search' => [
        'name'            => 'Jabatan',
        'department'      => 'Departemen',
        'employment-type' => 'Jenis Kepegawaian',
        'company'         => 'Perusahaan',
        'created-by'      => 'Dibuat Oleh',
    ],

    'form' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Informasi Kepegawaian',

                'fields' => [
                    'job-position-title'         => 'Nama Jabatan',
                    'job-position-title-tooltip' => 'Masukkan nama resmi jabatan',
                    'department'                 => 'Departemen',
                    'department-modal-title'     => 'Buat Departemen',
                    'job-location'               => 'Lokasi Pekerjaan',
                    'job-location-modal-title'   => 'Buat Alamat',
                    'industry'                   => 'Industri',
                    'company'                    => 'Perusahaan',
                    'company-modal-title'        => 'Buat Perusahaan',
                    'employment-type'            => 'Jenis Kepegawaian',
                    'interviewers'               => 'Pewawancara',
                    'recruiter'                  => 'Perekrut',
                    'recruiter-modal-title'      => 'Buat Perekrut',
                    'manager'                    => 'Manajer',
                ],
            ],

            'job-description' => [
                'title' => 'Deskripsi Pekerjaan',

                'fields' => [
                    'job-description'  => 'Deskripsi Pekerjaan',
                    'job-requirements' => 'Persyaratan Pekerjaan',
                ],
            ],

            'workforce-planning' => [
                'title' => 'Perencanaan Tenaga Kerja',

                'fields' => [
                    'recruitment-target' => 'Target Rekrutmen',
                    'date-from'          => 'Tanggal Dari',
                    'date-to'            => 'Tanggal Hingga',
                    'expected-skills'    => 'Keterampilan yang Diharapkan',
                    'employment-type'    => 'Jenis Kepegawaian',
                    'status'             => 'Status',
                ],
            ],

            'position-status' => [
                'title' => 'Status Jabatan',

                'fields' => [
                    'status' => 'Status',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Jabatan',
            'department'         => 'Departemen',
            'job-position'       => 'Jabatan',
            'company'            => 'Perusahaan',
            'expected-employees' => 'Perkiraan Karyawan',
            'current-employees'  => 'Karyawan Saat Ini',
            'status'             => 'Status',
            'created-by'         => 'Dibuat Oleh',
            'created-at'         => 'Dibuat Pada',
            'updated-at'         => 'Diperbarui Pada',
        ],

        'filters' => [
            'department'      => 'Departemen',
            'employment-type' => 'Jenis Kepegawaian',
            'job-position'    => 'Jabatan',
            'company'         => 'Perusahaan',
            'status'          => 'Status',
            'created-by'      => 'Dibuat Oleh',
            'updated-at'      => 'Diperbarui Pada',
            'created-at'      => 'Dibuat Pada',
        ],

        'groups' => [
            'job-position'    => 'Jabatan',
            'company'         => 'Perusahaan',
            'department'      => 'Departemen',
            'employment-type' => 'Jenis Kepegawaian',
            'created-by'      => 'Dibuat Oleh',
            'created-at'      => 'Dibuat Pada',
            'updated-at'      => 'Diperbarui Pada',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Jabatan dipulihkan',
                    'body'  => 'Jabatan berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jabatan dihapus',
                    'body'  => 'Jabatan berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Jabatan dipulihkan',
                    'body'  => 'Posisi pekerjaan berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Jabatan dihapus',
                    'body'  => 'Jabatan berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Jabatan dihapus permanen',
                    'body'  => 'Posisi pekerjaan berhasil dihapus paksa.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Jabatan',
                    'body'  => 'Jabatan berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'employment-information' => [
                'title' => 'Informasi Kepegawaian',

                'entries' => [
                    'job-position-title' => 'Nama Jabatan',
                    'department'         => 'Departemen',
                    'company'            => 'Perusahaan',
                    'employment-type'    => 'Jenis Kepegawaian',
                    'job-location'       => 'Lokasi Pekerjaan',
                    'industry'           => 'Industri',
                    'manager'            => 'Manajer',
                    'recruiter'          => 'Perekrut',
                    'interviewers'       => 'Pewawancara',
                ],
            ],
            'job-description' => [
                'title' => 'Deskripsi Pekerjaan',

                'entries' => [
                    'job-description'  => 'Deskripsi Pekerjaan',
                    'job-requirements' => 'Persyaratan Pekerjaan',
                ],
            ],
            'work-planning' => [
                'title' => 'Perencanaan Tenaga Kerja',

                'entries' => [
                    'expected-employees' => 'Perkiraan Karyawan',
                    'current-employees'  => 'Karyawan Saat Ini',
                    'date-from'          => 'Tanggal Dari',
                    'date-to'            => 'Tanggal Hingga',
                    'recruitment-target' => 'Target Rekrutmen',
                ],
            ],
            'position-status' => [
                'title' => 'Status Jabatan',

                'entries' => [
                    'status' => 'Status',
                ],
            ],
        ],
    ],
];
