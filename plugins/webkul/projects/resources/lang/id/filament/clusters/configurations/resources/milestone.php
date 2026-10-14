<?php

return [
    'navigation' => [
        'title' => 'Tonggak',
    ],

    'form' => [
        'name'         => 'Nama',
        'deadline'     => 'Batas Waktu',
        'is-completed' => 'Telah Selesai',
        'project'      => 'Proyek',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nama',
            'deadline'     => 'Batas Waktu',
            'is-completed' => 'Telah Selesai',
            'completed-at' => 'Diselesaikan Pada',
            'project'      => 'Proyek',
            'creator'      => 'Pembuat',
            'created-at'   => 'Dibuat Pada',
            'updated-at'   => 'Diperbarui Pada',
        ],

        'groups' => [
            'name'         => 'Nama',
            'is-completed' => 'Telah Selesai',
            'project'      => 'Proyek',
            'created-at'   => 'Dibuat Pada',
        ],

        'filters' => [
            'is-completed' => 'Telah Selesai',
            'project'      => 'Proyek',
            'creator'      => 'Pembuat',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Pembaruan tonggak pencapaian',
                    'body'  => 'Tonggak pencapaian berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tonggak pencapaian dihapus',
                    'body'  => 'Tonggak pencapaian berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tonggak dihapus',
                    'body'  => 'Tonggak pencapaian berhasil dihapus.',
                ],
            ],
        ],
    ],
];
