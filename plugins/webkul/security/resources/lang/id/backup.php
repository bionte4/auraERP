<?php

return [
    'errors' => [
        'unsupported-driver' => 'Driver database [:driver] tidak didukung untuk backup/restore.',
        'not-found'          => 'File backup [:file] tidak ditemukan.',
        'invalid-path'       => 'Path backup tidak valid.',
        'sqlite-missing'     => 'File database SQLite hilang atau memakai :memory:.',
        'write-failed'       => 'Gagal menulis file backup.',
        'decompress-failed'  => 'Gagal mengekstrak arsip backup.',
        'restore-disabled'   => 'Restore database dinonaktifkan di lingkungan ini. Unduh backup lalu restore di server staging.',
    ],

    'audit' => [
        'created'            => 'Backup database dibuat: :file',
        'restored'           => 'Database dipulihkan dari backup: :file',
        'deleted'            => 'Backup database dihapus: :file',
        'restore-blocked'    => 'Restore database diblokir untuk: :file',
        'employees-assigned' => 'Karyawan ditugaskan pada proyek :project (:count ditugaskan, :without tanpa Related User)',
        'plugin-installed'   => 'Plugin diinstal: :plugin',
        'plugin-uninstalled' => 'Plugin di-uninstall: :plugin',
    ],
];
