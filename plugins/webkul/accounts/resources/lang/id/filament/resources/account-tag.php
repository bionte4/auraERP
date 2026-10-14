<?php

return [
    'form' => [
        'fields' => [
            'color'         => 'Warna',
            'country'       => 'Negara',
            'applicability' => 'Keberlakuan',
            'name'          => 'Nama',
            'status'        => 'Status',
            'tax-negate'    => 'Negasi Pajak',
        ],
    ],

    'table' => [
        'columns' => [
            'color'         => 'Warna',
            'country'       => 'Negara',
            'created-by'    => 'Dibuat Oleh',
            'applicability' => 'Keberlakuan',
            'name'          => 'Nama',
            'status'        => 'Status',
            'tax-negate'    => 'Negasi Pajak',
            'created-at'    => 'Dibuat Pada',
            'updated-at'    => 'Diperbarui Pada',
            'deleted-at'    => 'Dihapus Pada',
        ],

        'filters' => [
            'bank'           => 'Bank',
            'account-holder' => 'Pemegang Rekening',
            'creator'        => 'Pembuat',
            'can-send-money' => 'Dapat Mengirim Uang',
        ],

        'groups' => [
            'country'       => 'Negara',
            'created-by'    => 'Dibuat Oleh',
            'applicability' => 'Keberlakuan',
            'name'          => 'Nama',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Tag Akun diperbarui',
                    'body'  => 'Tag akun berhasil diperbarui.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Tag Akun dihapus',
                    'body'  => 'Tag akun berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Tag Akun dihapus',
                    'body'  => 'Tag akun berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'color'         => 'Warna',
            'country'       => 'Negara',
            'applicability' => 'Keberlakuan',
            'name'          => 'Nama',
            'status'        => 'Status',
            'tax-negate'    => 'Negasi Pajak',
        ],
    ],
];
