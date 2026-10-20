<?php

return [
    'form' => [
        'fields' => [
            'web'     => 'Web',
            'sanctum' => 'Sanctum',
        ],
    ],

    'notification' => [
        'system-role-delete' => [
            'title' => 'Peran Sistem Tidak Dapat Dihapus',
            'body'  => 'Ini adalah peran sistem dan tidak dapat dihapus.',
        ],

        'baseline-role-delete' => [
            'title' => 'Peran Baseline Tidak Dapat Dihapus',
            'body'  => 'Peran ini bagian dari baseline security:sync-rbac. Ubah permission-nya, jangan dihapus.',
        ],

        'role-in-use-delete' => [
            'title' => 'Peran Masih Dipakai',
            'body'  => 'Peran ini masih dipakai oleh :count pengguna. Pindahkan mereka ke peran lain terlebih dahulu, lalu hapus peran ini.',
        ],

        'delete-role' => [
            'title'       => 'Hapus Peran :name',
            'description' => 'Guard: :guard · :permissions permission · :users pengguna terkait. Tindakan ini tidak dapat dibatalkan.',
        ],
    ],
];
