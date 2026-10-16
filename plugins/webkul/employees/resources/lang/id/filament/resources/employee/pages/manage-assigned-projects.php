<?php

return [
    'navigation' => [
        'title' => 'Proyek Ditugaskan',
    ],

    'title' => 'Proyek Ditugaskan — :name',

    'section' => [
        'title'       => 'Penugasan proyek',
        'description' => 'Pilih proyek yang diikuti karyawan ini. Akses disinkronkan lewat pengikut (followers) proyek jika sudah ada Pengguna Terkait/partner.',
    ],

    'fields' => [
        'projects'         => 'Proyek',
        'projects-helper'  => 'Anda juga dapat menugaskan karyawan dari Project → Tugaskan Karyawan.',
    ],

    'actions' => [
        'save' => 'Simpan Penugasan',
    ],

    'notifications' => [
        'saved' => [
            'title'     => 'Penugasan proyek disimpan',
            'body'      => ':count proyek ditugaskan ke karyawan ini.',
            'body-warn' => ':count proyek ditugaskan. Karyawan ini belum punya Pengguna Terkait — hubungkan akun user agar dapat mengakses proyek.',
        ],
    ],

    'table' => [
        'heading'  => 'Saat ini ditugaskan',
        'empty'    => 'Belum ada proyek yang ditugaskan ke karyawan ini.',
        'project'  => 'Proyek',
        'customer' => 'Pelanggan',
        'manager'  => 'Manajer Proyek',
        'none'     => '—',
    ],
];
