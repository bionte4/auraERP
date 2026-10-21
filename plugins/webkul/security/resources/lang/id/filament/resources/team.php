<?php

return [
    'title' => 'Tim',

    'navigation' => [
        'title' => 'Tim',
    ],

    'form' => [
        'fields' => [
            'name'          => 'Nama',
            'project'       => 'Proyek terkait',
            'project-help'  => 'Opsional. Jika diisi, sinkron anggota memakai proyek ini (lebih diutamakan daripada cocok nama).',
            'members'       => 'Anggota tim',
            'members-help'  => 'Anggota adalah akun user (login), bukan record karyawan. Untuk tim yang tertaut proyek, akun yang punya record karyawan otomatis mencerminkan roster proyek (Project Manager + karyawan ter-assign): karyawan yang dilepas dari proyek ikut keluar dari tim saat sinkron, sedangkan akun tanpa record karyawan tetap dipertahankan. Penugasan karyawan diatur lewat Project → Assign Employees.',
            'unsynced'      => 'Karyawan proyek yang tidak dapat tersinkron (belum ada Related User / akun login)',
            'unsynced-help' => 'Karyawan ini ter-assign ke proyek terkait tetapi belum memiliki akun login, sehingga tidak dapat menjadi anggota tim. Buatkan Related User dari Project → Assign Employees agar mereka ikut tersinkron.',
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
