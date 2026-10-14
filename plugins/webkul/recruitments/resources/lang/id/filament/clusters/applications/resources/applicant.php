<?php

return [
    'title' => 'Pelamar',

    'navigation' => [
        'title' => 'Pelamar',
    ],

    'global-search' => [
        'department' => 'Departemen',
        'work-email' => 'Email Kerja',
        'work-phone' => 'Telepon Kerja',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informasi Umum',

                'fields' => [
                    'evaluation-good'           => 'Evaluasi: Baik',
                    'evaluation-very-good'      => 'Evaluasi: Sangat Baik',
                    'evaluation-very-excellent' => 'Evaluasi: Sangat Baik Sekali',
                    'hired'                     => 'Direkrut',
                    'candidate-name'            => 'Nama kandidat',
                    'email'                     => 'Email',
                    'phone'                     => 'Telepon',
                    'linkedin-profile'          => 'Profil Linkedin',
                    'recruiter'                 => 'Perekrut',
                    'interviewer'               => 'Pewawancara',
                    'tags'                      => 'Tag',
                    'notes'                     => 'Catatan',
                    'hired-date'                => 'Tanggal Diterima',
                    'job-position'              => 'Jabatan',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Pendidikan & Ketersediaan',

                'fields' => [
                    'degree'            => 'Gelar',
                    'availability-date' => 'Tanggal Ketersediaan',
                ],
            ],

            'department' => [
                'title' => 'Departemen',
            ],

            'salary' => [
                'title' => 'Gaji yang Diharapkan & Diusulkan',

                'fields' => [
                    'expected-salary'       => 'Gaji yang Diharapkan',
                    'salary-proposed-extra' => 'Tunjangan Lain',
                    'proposed-salary'       => 'Gaji yang Diusulkan',
                    'salary-expected-extra' => 'Tunjangan Lain',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Sumber & Media',

                'fields' => [
                    'source' => 'Sumber',
                    'medium' => 'Sedang',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'partner-name'       => 'Nama Mitra',
            'applied-on'         => 'Diterapkan Pada',
            'job-position'       => 'Jabatan',
            'stage'              => 'Tahap',
            'candidate-name'     => 'Nama Kandidat',
            'evaluation'         => 'Evaluasi',
            'application-status' => 'Status Lamaran',
            'tags'               => 'Tag',
            'refuse-reason'      => 'Alasan Penolakan',
            'email'              => 'Email',
            'recruiter'          => 'Perekrut',
            'interviewer'        => 'Pewawancara',
            'candidate-phone'    => 'Telepon',
            'medium'             => 'Sedang',
            'source'             => 'Sumber',
            'salary-expected'    => 'Gaji yang Diharapkan',
            'availability-date'  => 'Tanggal Ketersediaan',
        ],

        'filters' => [
            'source'                  => 'Sumber',
            'medium'                  => 'Sedang',
            'candidate'               => 'Kandidat',
            'priority'                => 'Prioritas',
            'salary-proposed-extra'   => 'Tambahan Gaji yang Diusulkan',
            'salary-expected-extra'   => 'Tambahan Gaji yang Diharapkan',
            'applicant-notes'         => 'Catatan Pelamar',
            'create-date'             => 'Diterapkan Pada',
            'date-closed'             => 'Tanggal Diterima',
            'date-last-stage-updated' => 'Tahap Terakhir Diperbarui',
            'stage'                   => 'Tahap',
            'job-position'            => 'Jabatan',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Pelamar Dihapus',
                    'body'  => 'Pelamar berhasil dihapus.',
                ],
            ],
        ],

        'groups' => [
            'stage'          => 'Tahap',
            'job-position'   => 'Jabatan',
            'candidate-name' => 'Nama Kandidat',
            'responsible'    => 'Penanggung Jawab',
            'creation-date'  => 'Tanggal Pembuatan',
            'hired-date'     => 'Tanggal Diterima',
            'last-stage'     => 'Tahap Terakhir',
            'refuse-reason'  => 'Alasan Penolakan',
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Karyawan dihapus',
                    'body'  => 'Karyawan berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Karyawan dihapus',
                    'body'  => 'Karyawan berhasil dihapus.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Karyawan dipulihkan',
                    'body'  => 'Karyawan berhasil dipulihkan.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'evaluation-good'           => 'Evaluasi: Baik',
                    'evaluation-very-good'      => 'Evaluasi: Sangat Baik',
                    'evaluation-very-excellent' => 'Evaluasi: Sangat Baik Sekali',
                    'hired'                     => 'Direkrut',
                    'candidate-name'            => 'Nama kandidat',
                    'email'                     => 'Email',
                    'phone'                     => 'Telepon',
                    'linkedin-profile'          => 'Profil Linkedin',
                    'recruiter'                 => 'Perekrut',
                    'interviewer'               => 'Pewawancara',
                    'tags'                      => 'Tag',
                    'notes'                     => 'Catatan',
                    'job-position'              => 'Jabatan',
                ],
            ],

            'education-and-availability' => [
                'title' => 'Pendidikan & Ketersediaan',

                'entries' => [
                    'degree'            => 'Gelar',
                    'availability-date' => 'Tanggal Ketersediaan',
                ],
            ],

            'department' => [
                'title' => 'Departemen',
            ],

            'salary' => [
                'title' => 'Gaji yang Diharapkan & Diusulkan',

                'entries' => [
                    'expected-salary'       => 'Gaji yang Diharapkan',
                    'salary-proposed-extra' => 'Tunjangan Lain',
                    'proposed-salary'       => 'Gaji yang Diusulkan',
                    'salary-expected-extra' => 'Tunjangan Lain',
                ],
            ],

            'source-and-medium' => [
                'title' => 'Sumber & Media',

                'entries' => [
                    'source' => 'Sumber',
                    'medium' => 'Sedang',
                ],
            ],
        ],
    ],
];
