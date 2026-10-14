<?php

return [
    'title' => 'Tim Penjualan',

    'navigation' => [
        'title' => 'Tim Penjualan',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'     => 'Tim Penjualan',
                'status'   => 'Status',
                'fieldset' => [
                    'team-details' => [
                        'title'  => 'Detail Tim',
                        'fields' => [
                            'team-leader'            => 'Ketua Tim',
                            'company'                => 'Perusahaan',
                            'invoiced-target'        => 'Target Difakturkan',
                            'invoiced-target-suffix' => '/ Bulan',
                            'color'                  => 'Warna',
                            'members'                => 'Anggota',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'company'         => 'Perusahaan',
            'team-leader'     => 'Ketua Tim',
            'name'            => 'Nama',
            'status'          => 'Status',
            'invoiced-target' => 'Target Difakturkan',
            'color'           => 'Warna',
            'created-by'      => 'Dibuat Oleh',
            'created-at'      => 'Dibuat Pada',
            'updated-at'      => 'Diperbarui Pada',
        ],

        'filters' => [
            'name'        => 'Nama',
            'team-leader' => 'Ketua Tim',
            'company'     => 'Perusahaan',
            'created-by'  => 'Dibuat Oleh',
            'updated-at'  => 'Diperbarui Pada',
            'created-at'  => 'Dibuat Pada',
        ],

        'groups' => [
            'name'        => 'Nama',
            'company'     => ' Perusahaan',
            'team-leader' => 'Ketua Tim',
            'created-at'  => 'Dibuat Pada',
            'updated-at'  => 'Diperbarui Pada',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tim Penjualan dipulihkan',
                    'body'  => 'Tim penjualan berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tim Penjualan dihapus',
                    'body'  => 'Tim penjualan berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tim Penjualan dihapus permanen',
                    'body'  => 'Tim penjualan berhasil dihapus paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Tim Penjualan dipulihkan',
                    'body'  => 'Tim penjualan berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tim Penjualan dihapus',
                    'body'  => 'Tim penjualan berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Tim Penjualan dihapus permanen',
                    'body'  => 'Tim penjualan berhasil dihapus paksa.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Tim Penjualan dibuat',
                    'body'  => 'Tim penjualan berhasil dibuat.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'     => 'Tim Penjualan',
                'status'   => 'Status',
                'fieldset' => [
                    'team-details' => [
                        'title'   => 'Detail Tim',
                        'entries' => [
                            'team-leader'            => 'Ketua Tim',
                            'company'                => 'Perusahaan',
                            'invoiced-target'        => 'Target Difakturkan',
                            'invoiced-target-suffix' => '/ Bulan',
                            'color'                  => 'Warna',
                            'members'                => 'Anggota',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
