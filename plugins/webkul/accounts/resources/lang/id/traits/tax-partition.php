<?php

return [
    'form' => [
        'factor-percent'    => 'Persen Faktor',
        'factor-ratio'      => 'Rasio Faktor',
        'repartition-type'  => 'Jenis Pembagian',
        'document-type'     => 'Tipe Dokumen',
        'account'           => 'Akun',
        'tax'               => 'Pajak',
        'tax-closing-entry' => 'Entri Penutupan Pajak',
    ],

    'table' => [
        'columns' => [
            'factor-percent'    => 'Persen Faktor(%)',
            'account'           => 'Akun',
            'tax'               => 'Pajak',
            'company'           => 'Perusahaan',
            'repartition-type'  => 'Jenis Pembagian',
            'document-type'     => 'Tipe Dokumen',
            'tax-closing-entry' => 'Entri Penutupan Pajak',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Partisi Pajak diperbarui',
                    'body'  => 'Pembagian pajak berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Syarat Pembagian Pajak dihapus',
                    'body'  => 'Ketentuan pembagian pajak berhasil dihapus.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Syarat Pembagian Pajak dibuat',
                    'body'  => 'Ketentuan pembagian pajak berhasil dibuat.',
                ],
            ],
        ],
    ],
];
