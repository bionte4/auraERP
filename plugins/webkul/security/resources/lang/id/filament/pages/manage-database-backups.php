<?php

return [
    'title' => 'Backup Database',

    'navigation' => [
        'label' => 'Backup Database',
    ],

    'info' => [
        'heading'    => 'Backup berkala & restore',
        'enabled'    => 'Penjadwal: :status',
        'on'         => 'aktif',
        'off'        => 'nonaktif',
        'schedule'   => 'Backup otomatis berjalan setiap hari pukul :time (timezone server).',
        'retention'  => 'Retensi: :days hari, maks :count file.',
        'directory'  => 'Lokasi penyimpanan: :path',
        'restore'    => 'Restore dari UI: :status (default nonaktif di production)',
        'health'     => 'Kesehatan backup: :status — terakhir: :last — disk bebas: :free MB',
        'health-ok'  => 'Baik',
        'health-warn'=> 'Perlu perhatian',
        'cron'       => 'Pastikan cron Laravel scheduler aktif: * * * * * php /path/to/artisan schedule:run',
    ],

    'table' => [
        'filename'   => 'File',
        'created-at' => 'Dibuat',
        'size'       => 'Ukuran',
        'method'     => 'Metode / driver',
        'actions'    => 'Aksi',
        'empty'      => 'Belum ada backup. Buat manual atau tunggu jadwal harian.',
    ],

    'actions' => [
        'create'                 => 'Buat backup sekarang',
        'create-heading'         => 'Buat backup database?',
        'create-description'     => 'Sistem akan mengekspor database saat ini ke file backup di server.',
        'prune'                  => 'Bersihkan backup lama',
        'download'               => 'Unduh',
        'restore'                => 'Restore',
        'restore-heading'        => 'Pulihkan database dari backup?',
        'restore-description'    => 'Ini akan menimpa database aktif. Ketik :phrase untuk konfirmasi.',
        'restore-confirm-label'  => 'Ketik :phrase untuk konfirmasi',
        'restore-preconfirm'     => 'Lanjut ke konfirmasi restore?',
        'delete'                 => 'Hapus',
        'delete-confirm'         => 'Hapus file backup ini?',
    ],

    'notifications' => [
        'created' => [
            'title' => 'Backup dibuat',
            'body'  => 'File: :file',
        ],
        'restored' => [
            'title' => 'Database dipulihkan',
            'body'  => 'Dipulihkan dari: :file',
        ],
        'deleted' => [
            'title' => 'Backup dihapus',
        ],
        'pruned' => [
            'title' => 'Backup lama dibersihkan',
            'body'  => ':count file dihapus.',
        ],
        'failed' => [
            'title' => 'Operasi backup gagal',
        ],
    ],
];
