<?php

return [
    'title' => 'Tahap',

    'navigation' => [
        'title' => 'Tahap',
        'group' => 'Jabatan',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informasi Umum',

                'fields' => [
                    'stage-name'   => 'Nama Tahap',
                    'sort'         => 'Nomor Urut',
                    'requirements' => 'Persyaratan',
                ],
            ],

            'tooltips' => [
                'title'       => 'Tooltip',
                'description' => 'Tentukan label khusus untuk status lamaran.',

                'fields' => [
                    'gray-label'          => 'Label Abu-abu',
                    'gray-label-tooltip'  => 'Label untuk status abu-abu.',
                    'red-label'           => 'Label Merah',
                    'red-label-tooltip'   => 'Label untuk status merah.',
                    'green-label'         => 'Label Hijau',
                    'green-label-tooltip' => 'Label untuk status hijau.',
                ],
            ],

            'additional-information' => [
                'title' => 'Informasi Tambahan',

                'fields' => [
                    'job-positions' => 'Jabatan',
                    'folded'        => 'Dilipat',
                    'hired-stage'   => 'Tahap Diterima',
                    'default-stage' => 'Tahap Bawaan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Nama Tahap',
            'hired-stage'        => 'Tahap Diterima',
            'default-stage'      => 'Tahap Bawaan',
            'folded'             => 'Dilipat',
            'job-positions'      => 'Jabatan',
            'created-by'         => 'Dibuat Oleh',
            'created-at'         => 'Dibuat Pada',
            'updated-at'         => 'Diperbarui Pada',
        ],

        'filters' => [
            'name'         => 'Nama Tahap',
            'job-position' => 'Jabatan',
            'folded'       => 'Dilipat',
            'gray-label'   => 'Label Abu-abu',
            'red-label'    => 'Label Merah',
            'green-label'  => 'Label Hijau',
            'created-by'   => 'Dibuat Oleh',
            'created-at'   => 'Dibuat Pada',
            'updated-at'   => 'Diperbarui Pada',
        ],

        'groups' => [
            'job-position' => 'Jabatan',
            'stage-name'   => 'Nama Tahap',
            'folded'       => 'Dilipat',
            'gray-label'   => 'Label Abu-abu',
            'red-label'    => 'Label Merah',
            'green-label'  => 'Label Hijau',
            'created-by'   => 'Dibuat Oleh',
            'created-at'   => 'Dibuat Pada',
            'updated-at'   => 'Diperbarui Pada',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Tahapan dihapus',
                        'body'  => 'Tahap berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Tahap tidak dapat dihapus',
                        'body'  => 'Tahap tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tahapan dihapus',
                    'body'  => 'Tahap berhasil dihapus.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Tahap Baru',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'stage-name'   => 'Nama Tahap',
                    'sort'         => 'Nomor Urut',
                    'requirements' => 'Persyaratan',
                ],
            ],

            'tooltips' => [
                'title'       => 'Tooltip',
                'description' => 'Tentukan label khusus untuk status lamaran.',

                'entries' => [
                    'gray-label'          => 'Label Abu-abu',
                    'gray-label-tooltip'  => 'Label untuk status abu-abu.',
                    'red-label'           => 'Label Merah',
                    'red-label-tooltip'   => 'Label untuk status merah.',
                    'green-label'         => 'Label Hijau',
                    'green-label-tooltip' => 'Label untuk status hijau.',
                ],
            ],

            'additional-information' => [
                'title' => 'Informasi Tambahan',

                'entries' => [
                    'job-positions'      => 'Jabatan',
                    'folded'             => 'Dilipat',
                    'hired-stage'        => 'Tahap Diterima',
                    'default-stage'      => 'Tahap Bawaan',
                ],
            ],
        ],
    ],

];
