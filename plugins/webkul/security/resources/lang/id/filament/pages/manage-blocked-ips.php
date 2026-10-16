<?php

return [
    'title' => 'IP Diblokir',

    'navigation' => [
        'label' => 'IP Diblokir',
    ],

    'info' => [
        'heading'    => 'Rate limit login & blokir IP',
        'enabled'    => 'Proteksi: :status',
        'on'         => 'aktif',
        'off'        => 'nonaktif',
        'rate-limit' => 'Rate limit: :attempts percobaan gagal per email+IP dalam :minutes menit.',
        'ip-block'   => 'Blokir IP setelah :failures kegagalan dalam :window menit, selama :minutes menit.',
        'whitelist'  => 'Whitelist (tidak pernah diblokir): :ips',
    ],

    'table' => [
        'ip'            => 'Alamat IP',
        'attempts'      => 'Percobaan gagal',
        'reason'        => 'Alasan',
        'last-email'    => 'Email terakhir',
        'blocked-until' => 'Diblokir hingga',
        'actions'       => 'Aksi',
        'empty'         => 'Tidak ada IP yang sedang diblokir.',
    ],

    'actions' => [
        'refresh'         => 'Muat ulang',
        'unblock'         => 'Buka blokir',
        'unblock-confirm' => 'Buka blokir alamat IP ini?',
    ],

    'notifications' => [
        'unblocked' => [
            'title' => 'IP dibuka',
            'body'  => 'Alamat :ip dapat mencoba login lagi.',
        ],
    ],
];
