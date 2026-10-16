<?php

return [
    'title'  => 'Audit Trail',
    'plural' => 'Audit Trail',

    'navigation' => [
        'title' => 'Audit Trail',
    ],

    'actions' => [
        'back' => 'Kembali ke daftar',
    ],

    'table' => [
        'columns' => [
            'occurred-at'  => 'Waktu',
            'event'        => 'Peristiwa',
            'actor'        => 'Pelaku',
            'subject'      => 'Objek',
            'description'  => 'Deskripsi',
            'ip'           => 'Alamat IP',
            'guard'        => 'Guard',
            'user-agent'   => 'User agent',
            'properties'   => 'Detail',
        ],
        'filters' => [
            'event' => 'Peristiwa',
        ],
    ],

    'infolist' => [
        'sections' => [
            'overview' => 'Detail akses keamanan',
        ],
    ],
];
