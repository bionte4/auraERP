<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'            => 'Judul',
                'name'             => 'Nama',
                'type'             => 'Tipe',
                'create-type'      => 'Buat Jenis',
                'duration'         => 'Durasi',
                'start-date'       => 'Tanggal Mulai',
                'end-date'         => 'Tanggal Akhir',
                'display-type'     => 'Tipe Tampilan',
                'description'      => 'Deskripsi',
                'attachments'      => 'Lampiran',
                'file'             => 'Berkas',
                'file-helper-text' => 'Format yang diterima: PDF, DOC, DOCX, TXT, PNG, JPEG, dan WEBP. Maksimum 10 MB per berkas.',
                'attachment-name'  => 'Label',
                'add-attachment'   => 'Tambah Lampiran',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Judul',
            'start-date'   => 'Tanggal Mulai',
            'end-date'     => 'Tanggal Akhir',
            'display-type' => 'Tipe Tampilan',
            'description'  => 'Deskripsi',
            'created-by'   => 'Dibuat Oleh',
            'attachments'  => 'Lampiran',
            'created-at'   => 'Dibuat Pada',
            'updated-at'   => 'Diperbarui Pada',
        ],

        'groups' => [
            'group-by-type'         => 'Kelompokkan Berdasarkan Tipe',
            'group-by-display-type' => 'Kelompokkan Berdasarkan Jenis Tampilan',
        ],

        'header-actions' => [
            'add-resume' => 'Tambah Riwayat Hidup',
        ],

        'filters' => [
            'type'            => 'Tipe',
            'start-date-from' => 'Tanggal Mulai Dari',
            'start-date-to'   => 'Tanggal Mulai Hingga',
            'created-from'    => 'Dibuat Dari',
            'created-to'      => 'Dibuat Hingga',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Riwayat hidup diperbarui',
                    'body'  => 'Riwayat hidup berhasil diperbarui.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Riwayat hidup dibuat',
                    'body'  => 'Riwayat hidup berhasil dibuat.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Riwayat hidup dihapus',
                    'body'  => 'Riwayat hidup berhasil dihapus.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Resume dihapus',
                    'body'  => 'Riwayat hidup berhasil dihapus.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'           => 'Judul',
            'display-type'    => 'Tipe Tampilan',
            'type'            => 'Tipe',
            'description'     => 'Deskripsi',
            'duration'        => 'Durasi',
            'start-date'      => 'Tanggal Mulai',
            'end-date'        => 'Tanggal Akhir',
            'attachments'     => 'Lampiran',
            'file'            => 'Berkas',
            'attachment-name' => 'Label',
        ],
    ],
];
