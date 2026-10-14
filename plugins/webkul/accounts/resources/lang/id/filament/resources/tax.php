<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'name'                         => 'Nama',
                'tax-type'                     => 'Jenis Pajak',
                'tax-computation'              => 'Penghitungan Pajak',
                'tax-scope'                    => 'Cakupan Pajak',
                'status'                       => 'Status',
                'amount'                       => 'Jumlah',
                'formula'                      => 'Rumus',
                'formula-helper-text'          => 'Hanya aritmetika: angka, + - * / ( ), fungsi :functions, dan variabel berikut: :variables',
                'children-taxes'               => 'Pajak Turunan',
                'children-taxes-helper-text'   => 'Jumlah pajak ini adalah hasil penjumlahan pajak turunannya.',
                'children-taxes-type-mismatch' => 'Pajak turunan harus berjenis pajak :type atau tidak memiliki jenis pajak. Yang tidak memenuhi: :taxes',
            ],

            'repeater' => [
                'invoice-repartition-lines' => [
                    'label' => 'Baris Pembagian Faktur',
                ],

                'refund-repartition-lines' => [
                    'label' => 'Baris Pembagian Pengembalian Dana',
                ],

                'fields' => [
                    'type'           => 'Tipe',
                    'factor-percent' => 'Faktor %',
                    'account'        => 'Akun',
                ],
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Opsi Lanjutan',

                    'fields' => [
                        'invoice-label'       => 'Label faktur',
                        'tax-group'           => 'Grup Pajak',
                        'country'             => 'Negara',
                        'include-in-price'    => 'Termasuk dalam Harga',
                        'include-base-amount' => 'Memengaruhi Dasar Pajak Berikutnya',
                        'is-base-affected'    => 'Dasar Dipengaruhi Pajak Sebelumnya',
                    ],
                ],

                'fields' => [
                    'description' => 'Deskripsi',
                    'legal-notes' => 'Catatan Hukum',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                   => 'Nama',
            'amount-type'            => 'Jenis Jumlah',
            'company'                => 'Perusahaan',
            'tax-group'              => 'Grup Pajak',
            'country'                => 'Negara',
            'tax-type'               => 'Jenis Pajak',
            'tax-scope'              => 'Cakupan Pajak',
            'amount-type'            => 'Jenis Jumlah',
            'invoice-label'          => 'Label Faktur',
            'tax-exigibility'        => 'Saat Pajak Terutang',
            'price-include-override' => 'Timpa Harga Termasuk',
            'amount'                 => 'Jumlah',
            'status'                 => 'Status',
            'include-base-amount'    => 'Sertakan Jumlah Dasar',
            'is-base-affected'       => 'Basis Terpengaruh',
        ],

        'groups' => [
            'name'         => 'Nama',
            'company'      => 'Perusahaan',
            'tax-group'    => 'Grup Pajak',
            'country'      => 'Negara',
            'created-by'   => 'Dibuat Oleh',
            'type-tax-use' => 'Tipe Penggunaan Pajak',
            'tax-scope'    => 'Cakupan Pajak',
            'amount-type'  => 'Jenis Jumlah',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pajak dihapus',
                        'body'  => 'Pajak berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Pajak tidak dapat dihapus',
                        'body'  => 'Pajak tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Pajak dihapus',
                        'body'  => 'Pajak berhasil dihapus.',
                    ],

                    'error' => [
                        'title' => 'Pajak tidak dapat dihapus',
                        'body'  => 'Pajak tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],
        ],

        'pages' => [
            'create' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Baris Pembagian Tidak Valid',
                    ],
                ],
            ],

            'edit' => [
                'notifications' => [
                    'invalid-repartition-lines' => [
                        'title' => 'Baris Pembagian Tidak Valid',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'            => 'Nama',
                'tax-type'        => 'Jenis Pajak',
                'tax-computation' => 'Penghitungan Pajak',
                'tax-scope'       => 'Cakupan Pajak',
                'status'          => 'Status',
                'amount'          => 'Jumlah',
                'formula'         => 'Rumus',
                'children-taxes'  => 'Pajak Turunan',
            ],

            'field-set' => [
                'advanced-options' => [
                    'title' => 'Opsi Lanjutan',

                    'entries' => [
                        'invoice-label'       => 'Label faktur',
                        'tax-group'           => 'Grup Pajak',
                        'country'             => 'Negara',
                        'include-in-price'    => 'Sertakan dalam harga',
                        'include-base-amount' => 'Sertakan jumlah dasar',
                        'is-base-affected'    => 'Basis terpengaruh',
                    ],
                ],

                'description-and-legal-notes' => [
                    'title'   => 'Deskripsi & Catatan Hukum Faktur',
                    'entries' => [
                        'description' => 'Deskripsi',
                        'legal-notes' => 'Catatan Hukum',
                    ],
                ],
            ],
        ],
    ],

];
