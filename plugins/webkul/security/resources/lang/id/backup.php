<?php

return [
    'errors' => [
        'unsupported-driver' => 'Driver database [:driver] tidak didukung untuk backup/restore.',
        'not-found'          => 'File backup [:file] tidak ditemukan.',
        'invalid-path'       => 'Path backup tidak valid.',
        'sqlite-missing'     => 'File database SQLite hilang atau memakai :memory:.',
        'write-failed'       => 'Gagal menulis file backup.',
        'decompress-failed'  => 'Gagal mengekstrak arsip backup.',
    ],

    'audit' => [
        'created'  => 'Backup database dibuat: :file',
        'restored' => 'Database dipulihkan dari backup: :file',
        'deleted'  => 'Backup database dihapus: :file',
    ],
];
