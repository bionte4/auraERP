<?php

return [
    'title' => 'Tim',

    'navigation' => [
        'title' => 'Tim',
    ],

    'form' => [
        'fields' => [
            'name'            => 'Nama',
            'project'         => 'Proyek terkait',
            'project-help'    => 'Opsional. Jika diisi, sinkron anggota memakai proyek ini (lebih diutamakan daripada cocok nama).',
            'members'         => 'Anggota (karyawan)',
            'members-help'    => 'Pilih karyawan, termasuk yang belum punya akun login (bertanda Tanpa login). Menyimpan daftar ini menugaskan mereka ke proyek terkait. Karyawan yang punya Related User juga masuk keanggotaan Tim untuk hak akses Group.',
            'members-search'  => 'Cari karyawan…',
            'members-note'    => 'Karyawan tanpa login tetap bisa ditugaskan ke proyek. Mereka belum bisa masuk aplikasi sampai Related User dibuat (Project → Tugaskan Karyawan).',
            'no-login'        => 'Tanpa login',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nama',
            'members'      => 'Anggota',
            'member-names' => 'Nama anggota',
            'created-by'   => 'Dibuat Oleh',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tim diperbarui',
                    'body'  => 'Tim berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'modal' => [
                    'heading'             => 'Hapus tim?',
                    'description'         => 'Tim ":name" akan dihapus. Tindakan ini tidak dapat dibatalkan.',
                    'description-members' => 'Tim ":name" masih memiliki :count anggota. Anggota akan dilepas dari tim terlebih dahulu, lalu tim dihapus. Tindakan ini tidak dapat dibatalkan.',
                    'submit'              => 'Ya, hapus tim',
                ],
                'notification' => [
                    'title' => 'Tim dihapus',
                    'body'  => 'Tim berhasil dihapus.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Tim dibuat',
                    'body'  => 'Tim berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Nama',
            'members' => 'Anggota tim',
        ],
    ],
];
