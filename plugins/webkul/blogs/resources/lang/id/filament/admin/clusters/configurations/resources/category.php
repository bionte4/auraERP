<?php

return [
    'navigation' => [
        'title' => 'Kategori',
        'group' => 'Blog',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Nama',
            'name-placeholder' => 'Judul kategori ...',
            'sub-title'        => 'Subjudul',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'nama',
            'sub-title'  => 'Subjudul',
            'posts'      => 'Postingan',
            'created-at' => 'Dibuat Pada',
        ],

        'filters' => [
            'is-published' => 'Telah Diterbitkan',
            'author'       => 'Penulis',
            'creator'      => 'Dibuat Oleh',
            'category'     => 'Kategori',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kategori diperbarui',
                    'body'  => 'Kategori berhasil diperbarui.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Kategori dipulihkan',
                    'body'  => 'Kategori berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kategori dihapus',
                    'body'  => 'Kategori berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kategori dihapus permanen',
                        'body'  => 'Kategori berhasil dihapus paksa.',
                    ],
                    'error' => [
                        'title' => 'Kategori  tidak dapat dihapus',
                        'body'  => 'Kategori tidak dapat dihapus karena sedang digunakan.',
                    ],
                ],
            ],

            'force-delete-error' => [
                'notification' => [
                    'title' => 'Tidak dapat menghapus kategori',
                    'body'  => 'Anda tidak dapat menghapus kategori ini karena terkait dengan beberapa postingan.',
                ],

                'exception' => 'Anda tidak dapat menghapus paksa kategori ini karena terkait dengan beberapa postingan.',
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kategori dipulihkan',
                    'body'  => 'Kategori berhasil dipulihkan.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kategori dihapus',
                    'body'  => 'Kategori berhasil dihapus.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Kategori dihapus permanen',
                    'body'  => 'Kategori berhasil dihapus paksa.',
                ],
            ],

            'force-delete-error' => [
                'notification' => [
                    'title' => 'Tidak dapat menghapus kategori',
                    'body'  => 'Anda tidak dapat menghapus kategori ini karena terkait dengan beberapa postingan.',
                ],
            ],
        ],
    ],

    'infolist' => [
    ],
];
