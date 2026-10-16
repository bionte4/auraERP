<?php

return [
    'errors' => [
        'ip-blocked'  => 'Terlalu banyak percobaan login gagal dari IP ini. Coba lagi dalam sekitar :minutes menit.',
        'throttled'   => 'Terlalu banyak percobaan login. Tunggu :seconds detik lalu coba lagi.',
    ],

    'audit' => [
        'blocked'   => 'IP :ip diblokir selama :minutes menit',
        'unblocked' => 'IP :ip dibuka blokirnya',
    ],
];
