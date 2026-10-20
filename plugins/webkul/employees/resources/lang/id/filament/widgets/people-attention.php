<?php

return [
    'heading' => [
        'title' => 'Perlu Perhatian',
    ],

    'table-columns' => [
        'employee'   => 'Karyawan',
        'department' => 'Departemen',
        'manager'    => 'Atasan',
        'issues'     => 'Masalah',
        'projects'   => 'Proyek',
        'join-date'  => 'Tanggal Bergabung',
    ],

    'issues' => [
        'no-user'       => 'Tanpa Related User',
        'no-department' => 'Tanpa departemen',
        'no-job'        => 'Tanpa jabatan',
        'no-manager'    => 'Tanpa atasan',
        'no-email'      => 'Tanpa email',
        'no-project'    => 'Tanpa proyek',
    ],

    'filters' => [
        'issue' => [
            'label'      => 'Masalah',
            'all'        => 'Semua masalah',
            'no-user'    => 'Tanpa Related User',
            'incomplete' => 'Profil belum lengkap',
            'no-project' => 'Tanpa proyek',
        ],
    ],

    'empty' => [
        'heading'     => 'Tidak ada karyawan yang perlu perhatian',
        'description' => 'Karyawan aktif tanpa login, profil dasar belum lengkap, atau belum ditugaskan ke proyek akan muncul di sini.',
    ],
];
