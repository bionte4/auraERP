<?php

return [
    'navigation' => [
        'title' => 'Halaman',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Umum',

                'fields' => [
                    'title'             => 'Judul',
                    'title-placeholder' => 'Judul halaman ...',
                    'slug'              => 'Slug',
                    'content'           => 'Konten',
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
                    'is-header-visible' => 'Menu Header Terlihat',
                    'is-footer-visible' => 'Menu Footer Terlihat',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'             => 'Judul',
            'slug'              => 'Slug',
            'creator'           => 'Dibuat Oleh',
            'is-published'      => 'Telah Diterbitkan',
            'is-header-visible' => 'Menu Header Terlihat',
            'is-footer-visible' => 'Menu Footer Terlihat',
            'created-at'        => 'Dibuat Pada',
            'updated-at'        => 'Diperbarui Pada',
        ],

        'groups' => [
            'created-at' => 'Dibuat Pada',
        ],

        'filters' => [
            'is-published' => 'Telah Diterbitkan',
            'creator'      => 'Dibuat Oleh',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Halaman diperbarui',
                    'body'  => 'Halaman berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Halaman dipulihkan',
                    'body'  => 'Halaman berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Halaman dihapus',
                    'body'  => 'Halaman berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Halaman dihapus paksa',
                    'body'  => 'Halaman berhasil dihapus secara paksa.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Halaman dipulihkan',
                    'body'  => 'Halaman berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Halaman dihapus',
                    'body'  => 'Halaman berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Halaman dihapus paksa',
                    'body'  => 'Halaman berhasil dihapus secara paksa.',
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
                    'is-header-visible' => 'Menu Header Terlihat',
                    'is-footer-visible' => 'Menu Footer Terlihat',
                ],
            ],
        ],
    ],
];
