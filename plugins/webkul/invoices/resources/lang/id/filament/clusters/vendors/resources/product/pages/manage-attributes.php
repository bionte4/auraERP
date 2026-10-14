<?php

return [
    'title' => 'Atribut',

    'form' => [
        'attribute' => 'Atribut',
        'values'    => 'Nilai',
    ],

    'table' => [
        'description' => 'Peringatan: menambah atau menghapus atribut akan menghapus dan membuat ulang varian yang ada serta menyebabkan hilangnya kemungkinan penyesuaiannya.',

        'header-actions' => [
            'create' => [
                'label' => 'Tambah Atribut',

                'notification' => [
                    'title' => 'Atribut dibuat',
                    'body'  => 'Atribut berhasil dibuat.',
                ],
            ],
        ],

        'columns' => [
            'attribute' => 'Atribut',
            'values'    => 'Nilai',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Atribut diperbarui',
                    'body'  => 'Atribut berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Atribut dihapus',
                    'body'  => 'Atribut berhasil dihapus.',
                ],
            ],
        ],
    ],
];
