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
            'members-help'  => 'Pilih user yang menjadi anggota tim ini. Manage Teams menghitung proyek di mana anggota ini = Manajer Proyek.',
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
