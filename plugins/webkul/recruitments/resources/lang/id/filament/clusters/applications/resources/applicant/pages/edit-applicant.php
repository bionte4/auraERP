<?php

return [
    'create-employee' => 'Buat Karyawan',
    'goto-employee'   => 'Buka Karyawan',

    'notification' => [
        'title' => 'Pelamar diperbarui',
        'body'  => 'Pelamar berhasil diperbarui.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Pelamar dihapus',
                'body'  => 'Pelamar berhasil dihapus.',
            ],
        ],
        'force-delete' => [
            'notification' => [
                'title' => 'Pelamar dihapus',
                'body'  => 'Pelamar berhasil dihapus paksa.',
            ],
        ],

        'restore' => [
            'notification' => [
                'title' => 'Pelamar dipulihkan',
                'body'  => 'Pelamar berhasil dipulihkan.',
            ],
        ],

        'refuse' => [
            'title'        => 'Alasan Penolakan',
            'form'         => [
                'fields' => [
                    'notify'   => 'Beri Tahu',
                    'email-to' => 'Email Ke',
                ],
            ],
            'notification' => [
                'title' => 'Pelamar ditolak',
                'body'  => 'Pelamar berhasil ditolak.',
            ],
        ],

        'reopen' => [
            'title'        => 'Buka Kembali Pelamar',
            'notification' => [
                'title' => 'Pelamar dibuka kembali',
                'body'  => 'Pelamar berhasil dibuka kembali.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'Status pelamar diperbarui',
                'body'  => 'Status pelamar berhasil diperbarui.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Lamaran Kerja Anda: :application',
        ],

        'application-confirm' => [
            'subject' => 'Lamaran pekerjaan Anda: :job_position',
        ],
        'interviewer-assigned' => [
            'subject' => 'Anda telah ditugaskan pada Pelamar :applicant.',
        ],
    ],
];
