<?php

return [
    'title' => 'Tim',

    'navigation' => [
        'title' => 'Tim',
    ],

    'form' => [
        'fields' => [
            'name'                    => 'Nama',
            'project'                 => 'Proyek terkait',
            'project-help'            => 'Opsional. Jika diisi, sinkron anggota memakai proyek ini (lebih diutamakan daripada cocok nama).',
            'members'                 => 'Tambah / lepas anggota',
            'members-help'            => 'Daftar di atas adalah karyawan yang sudah di proyek. Centang untuk menambah atau melepas. Ketik nama di pencarian untuk menemukan karyawan lain (termasuk yang belum punya login).',
            'members-search'          => 'Cari karyawan untuk ditambahkan…',
            'members-note'            => 'Karyawan tanpa login tetap bisa ditugaskan ke proyek. Mereka belum bisa masuk aplikasi sampai Related User dibuat (Project → Tugaskan Karyawan).',
            'current-members'         => 'Karyawan sudah di proyek ini',
            'current-members-empty'   => 'Belum ada karyawan di proyek ini. Cari nama di bawah untuk menambahkan.',
            'no-login'                => 'Tanpa login',
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
