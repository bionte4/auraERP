<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Pelamar dihapus',
                'body'  => 'Pelamar berhasil dihapus.',
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
            'title'        => 'Pulihkan Pelamar dari penolakan',
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
    ],
];
