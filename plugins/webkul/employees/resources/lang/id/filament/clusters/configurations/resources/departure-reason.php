<?php

return [
    'title' => 'Alasan Kepergian',

    'navigation' => [
        'title' => 'Alasan Kepergian',
        'group' => 'Karyawan',
    ],

    'groups' => [
        'status'     => 'Status',
        'created-by' => 'Dibuat Oleh',
        'created-at' => 'Dibuat Pada',
        'updated-at' => 'Diperbarui Pada',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nama',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Nama',
            'created-by' => 'Dibuat Oleh',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'name'       => 'Nama',
            'employee'   => 'Karyawan',
            'created-by' => 'Dibuat Oleh',
            'updated-at' => 'Diperbarui Pada',
            'created-at' => 'Dibuat Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Alasan berhenti diperbarui',
                    'body'  => 'Alasan keluar berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Alasan berhenti dihapus',
                    'body'  => 'Alasan keluar berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Alasan berhenti dihapus',
                    'body'  => 'Alasan keluar berhasil dihapus.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Alasan berhenti dibuat',
                    'body'  => 'Alasan keluar berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name' => 'Nama',
    ],
];
