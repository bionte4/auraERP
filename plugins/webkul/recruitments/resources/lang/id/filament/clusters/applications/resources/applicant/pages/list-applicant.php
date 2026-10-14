<?php

return [
    'notification' => [
        'title' => 'Pelamar dibuat',
        'body'  => 'Pelamar berhasil dibuat.',
    ],

    'tabs' => [
        'my-applicants'          => 'Pelamar Saya',
        'un-assigned'            => 'Belum Ditugaskan',
        'in-progress'            => 'Sedang Berjalan',
        'hired'                  => 'Direkrut',
        'refused'                => 'Ditolak',
        'archived'               => 'Diarsipkan',
        'blocked'                => 'Diblokir',
        'directly-available'     => 'Langsung Tersedia',
        'created-recently'       => 'Baru Dibuat',
        'stage-updated-recently' => 'Tahap Baru Saja Diperbarui',
    ],

    'header-actions' => [
        'create-applicant' => [
            'label' => 'Pelamar Baru',

            'modal-title' => 'Buat Pelamar',

            'form' => [
                'fields' => [
                    'candidate' => 'Kandidat',
                ],
            ],
        ],
    ],
];
