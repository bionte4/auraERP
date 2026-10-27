<?php

return [
    'navigation' => [
        'title'         => 'Klien multi-proyek',
        'badge-tooltip' => 'Klien dengan dua atau lebih proyek aktif',
    ],
    'title'       => 'Klien dengan lebih dari satu proyek',
    'description' => 'Klien yang saat ini memiliki dua atau lebih Customer Projects aktif. Satu klien boleh memegang beberapa kontrak.',
    'columns'     => [
        'customer'    => 'Klien',
        'projects'    => 'Proyek',
        'total-value' => 'Total nilai',
        'project-list'=> 'Nama proyek',
    ],
    'actions' => [
        'view-customer' => 'Lihat klien',
        'view-projects' => 'Lihat proyek',
    ],
    'empty' => [
        'heading'     => 'Tidak ada klien multi-proyek',
        'description' => 'Belum ada klien yang memiliki lebih dari satu proyek aktif.',
    ],
];
