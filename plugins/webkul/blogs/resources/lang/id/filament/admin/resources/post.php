<?php

return [
    'navigation' => [
        'title' => 'Postingan Blog',
    ],

    'global-search' => [
        'author' => 'Penulis',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'title'             => 'Judul',
                    'sub-title'         => 'Subjudul',
                    'title-placeholder' => 'Judul artikel ...',
                    'slug'              => 'Slug',
                    'content'           => 'Konten',
                    'banner'            => 'Banner',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'fields' => [
                    'meta-title'       => 'Judul Meta',
                    'meta-keywords'    => 'Kata Kunci Meta',
                    'meta-description' => 'Deskripsi Meta',
                ],
            ],

            'settings' => [
                'title' => 'Pengaturan',

                'fields' => [
                    'category'     => 'Kategori',
                    'tags'         => 'Tag',
                    'name'         => 'Nama',
                    'color'        => 'Warna',
                    'is-published' => 'Telah Diterbitkan',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Judul',
            'slug'         => 'Slug',
            'author'       => 'Penulis',
            'category'     => 'Kategori',
            'creator'      => 'Dibuat Oleh',
            'is-published' => 'Telah Diterbitkan',
            'created-at'   => 'Dibuat Pada',
            'updated-at'   => 'Diperbarui Pada',
        ],

        'groups' => [
            'category'   => 'Kategori',
            'author'     => 'Penulis',
            'created-at' => 'Dibuat Pada',
        ],

        'filters' => [
            'is-published' => 'Telah Diterbitkan',
            'author'       => 'Penulis',
            'creator'      => 'Dibuat Oleh',
            'category'     => 'Kategori',
            'tags'         => 'Tag',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Artikel diperbarui',
                    'body'  => 'Postingan berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Artikel dipulihkan',
                    'body'  => 'Postingan berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Artikel dihapus',
                    'body'  => 'Postingan berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Postingan dihapus paksa',
                    'body'  => 'Postingan berhasil dihapus secara paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Artikel dipulihkan',
                    'body'  => 'Postingan berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Artikel dihapus',
                    'body'  => 'Postingan berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Postingan dihapus paksa',
                    'body'  => 'Postingan berhasil dihapus secara paksa.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'entries' => [
                    'title'   => 'Judul',
                    'slug'    => 'Slug',
                    'content' => 'Konten',
                    'banner'  => 'Banner',
                ],
            ],

            'seo' => [
                'title' => 'SEO',

                'entries' => [
                    'meta-title'       => 'Judul Meta',
                    'meta-keywords'    => 'Kata Kunci Meta',
                    'meta-description' => 'Deskripsi Meta',
                ],
            ],

            'record-information' => [
                'title' => 'Informasi Rekaman',

                'entries' => [
                    'author'          => 'Penulis',
                    'created-by'      => 'Dibuat Oleh',
                    'published-at'    => 'Diterbitkan Pada',
                    'last-updated-by' => 'Terakhir Diperbarui Oleh',
                    'last-updated'    => 'Terakhir Diperbarui Pada',
                    'created-at'      => 'Dibuat Pada',
                ],
            ],

            'settings' => [
                'title' => 'Pengaturan',

                'entries' => [
                    'category'     => 'Kategori',
                    'tags'         => 'Tag',
                    'name'         => 'Nama',
                    'color'        => 'Warna',
                    'is-published' => 'Telah Diterbitkan',
                ],
            ],
        ],
    ],
];
