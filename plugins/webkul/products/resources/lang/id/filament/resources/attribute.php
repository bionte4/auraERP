<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Umum',

                'fields' => [
                    'name' => 'Nama',
                    'type' => 'Tipe',
                ],
            ],

            'options' => [
                'title'  => 'Opsi',

                'fields' => [
                    'name'        => 'Nama',
                    'color'       => 'Warna',
                    'extra-price' => 'Harga Tambahan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Nama',
            'type'        => 'Tipe',
            'deleted-at'  => 'Dihapus Pada',
            'created-at'  => 'Dibuat Pada',
            'updated-at'  => 'Diperbarui Pada',
        ],

        'groups' => [
            'type'       => 'Tipe',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'filters' => [
            'type' => 'Tipe',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Atribut dipulihkan',
                    'body'  => 'Atribut berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atribut dihapus',
                    'body'  => 'Atribut berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Atribut dihapus permanen',
                        'body'  => 'Atribut berhasil dihapus paksa.',
                    ],

                    'error' => [
                        'title' => 'Atribut tidak dapat dihapus',
                        'body'  => 'Atribut tidak dapat dihapus karena sedang digunakan oleh Produk : :products',
                        'more'  => '... +:count lainnya',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Atribut dipulihkan',
                    'body'  => 'Atribut berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atribut dihapus',
                    'body'  => 'Atribut berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Atribut dihapus permanen',
                        'body'  => 'Atribut berhasil dihapus paksa.',
                    ],

                    'partial' => [
                        'title' => 'Beberapa atribut tidak dapat dihapus',
                        'body'  => 'Atribut ":attributes" tidak dapat dihapus karena sedang digunakan oleh Produk : :products',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Informasi Umum',

                'entries' => [
                    'name' => 'Nama',
                    'type' => 'Tipe',
                ],
            ],

            'record-information' => [
                'title' => 'Informasi Rekaman',

                'entries' => [
                    'creator'    => 'Dibuat Oleh',
                    'created_at' => 'Dibuat Pada',
                    'updated_at' => 'Terakhir Diperbarui Pada',
                ],
            ],
        ],
    ],
];
