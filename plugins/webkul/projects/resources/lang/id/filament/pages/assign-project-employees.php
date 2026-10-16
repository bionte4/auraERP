<?php

return [
    'navigation' => [
        'title' => 'Tugaskan Karyawan',
    ],

    'title'       => 'Tugaskan Karyawan ke Proyek',
    'description' => 'Pilih proyek, lalu pilih karyawan yang menjadi anggota proyek tersebut. Karyawan yang punya Pengguna Terkait juga otomatis mendapat akses proyek sebagai pengikut (followers).',

    'fields' => [
        'project'           => 'Proyek',
        'employees'         => 'Karyawan',
        'employees-helper'  => 'Utamakan karyawan yang sudah punya Pengguna Terkait agar dapat login dan mengakses proyek.',
    ],

    'actions' => [
        'save' => 'Simpan Penugasan',
    ],

    'notifications' => [
        'saved' => [
            'title'       => 'Penugasan karyawan disimpan',
            'body'        => ':count karyawan ditugaskan ke proyek.',
            'body-warn'   => ':count karyawan ditugaskan. :without di antaranya belum punya Pengguna Terkait — hubungkan akun user agar mereka dapat mengakses proyek.',
        ],
        'missing-project' => [
            'title' => 'Silakan pilih proyek terlebih dahulu',
        ],
    ],

    'table' => [
        'heading'      => 'Saat ini ditugaskan',
        'empty'        => 'Belum ada karyawan yang ditugaskan ke proyek ini.',
        'name'         => 'Karyawan',
        'department'   => 'Departemen',
        'related-user' => 'Pengguna Terkait',
        'none'         => '—',
    ],
];
