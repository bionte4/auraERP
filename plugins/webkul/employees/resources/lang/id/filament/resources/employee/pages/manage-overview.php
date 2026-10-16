<?php

return [
    'navigation' => [
        'label' => 'Ringkasan 360',
    ],
    'title'   => 'Ringkasan — :name',
    'actions' => [
        'provision' => 'Buat Related User',
    ],
    'cards' => [
        'department'   => 'Departemen',
        'related-user' => 'Related User',
        'capacity'     => 'Kapasitas minggu ini',
        'contract'     => 'Akhir kontrak',
        'leave'        => 'Pengajuan cuti (YTD)',
        'assets'       => 'Aset dipegang',
        'evaluation'   => 'Evaluasi terakhir',
    ],
    'sections' => [
        'projects' => 'Proyek ditugaskan',
    ],
    'provision' => [
        'errors' => [
            'email'       => 'Karyawan perlu work email valid sebelum user dibuat.',
            'email-taken' => 'Email tersebut sudah terhubung ke karyawan lain.',
        ],
    ],
    'notifications' => [
        'provisioned' => [
            'title' => 'Related User siap',
            'body'  => 'Email: :email — password sementara: :password',
        ],
    ],
];
