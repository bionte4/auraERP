<?php

return [
    'setup' => [
        'title'               => 'Jadwalkan Aktivitas',
        'submit-action-title' => 'Jadwal',

        'form' => [
            'fields' => [
                'activity-plan' => 'Rencana Aktivitas',
                'plan-date'     => 'Tanggal Rencana',
                'plan-summary'  => 'Ringkasan Rencana',
                'activity-type' => 'Tipe Aktivitas',
                'due-date'      => 'Tanggal Jatuh Tempo',
                'summary'       => 'Ringkasan',
                'assigned-to'   => 'Ditugaskan Kepada',
                'log-note'      => 'Catat catatan',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Aktivitas Dibuat',
                    'body'  => 'Aktivitas telah dibuat.',
                ],

                'warning'  => [
                    'title' => 'Tidak ada berkas baru',
                    'body'  => 'Semua berkas sudah diunggah.',
                ],

                'error' => [
                    'title' => 'Pembuatan aktivitas gagal',
                    'body'  => 'Gagal membuat aktivitas ',
                ],
            ],
        ],
    ],
];
