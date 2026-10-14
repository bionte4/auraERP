<?php

return [
    'title' => 'Atur Ulang ke Draf',

    'validation' => [
        'notification' => [
            'error' => [
                'invalid-state' => [
                    'title' => 'Status Entri Jurnal Tidak Valid',
                    'body'  => 'Hanya entri jurnal yang diposting atau dibatalkan yang dapat diatur ulang menjadi draf.',
                ],
            ],
        ],
    ],
];
