<?php

return [
    'title' => 'Tim',

    'navigation' => [
        'title' => 'Tim',
    ],

    'form' => [
        'fields' => [
            'name'          => 'Nama',
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
