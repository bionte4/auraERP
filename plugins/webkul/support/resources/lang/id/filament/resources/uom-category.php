<?php

return [
    'navigation' => [
        'title' => 'Kategori UOM',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'name' => 'Nama',
                ],
            ],

            'uoms' => [
                'title' => 'Satuan Ukur',

                'fields' => [
                    'uoms'     => 'Satuan',
                    'type'     => 'Tipe',
                    'name'     => 'Satuan ukur',
                    'ratio'    => 'Rasio',
                    'rounding' => 'Presisi Pembulatan',
                ],

                'validations' => [
                    'missing-reference'          => 'Kategori ini harus memiliki satuan ukur referensi.',
                    'multiple-references'        => 'Kategori ini hanya boleh memiliki satu satuan ukur referensi.',
                    'ratio-greater-than-zero'    => 'Rasio konversi satuan ukur tidak boleh nol.',
                    'rounding-greater-than-zero' => 'Presisi pembulatan harus lebih besar dari nol.',
                ],

                'actions' => [
                    'add' => 'Tambah Satuan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nama',
            'uoms'       => 'UOMs',
            'created-at' => 'Dibuat Pada',
            'updated-at' => 'Diperbarui Pada',
        ],

        'groups' => [
            'created-at' => 'Dibuat Pada',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kategori UOM diperbarui',
                    'body'  => 'Kategori UOM berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kategori UOM dihapus',
                    'body'  => 'Kategori UOM berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kategori UOM dihapus',
                    'body'  => 'Kategori UOM berhasil dihapus.',
                ],
            ],
        ],
    ],
];
