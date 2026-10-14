<?php

return [
    'navigation' => [
        'title' => 'Lokasi kerja',
    ],

    'model'  => 'Lokasi kerja',
    'plural' => 'Lokasi kerja',

    'actions' => [
        'create' => 'Lokasi baru',
    ],

    'form' => [
        'name'        => 'Nama lokasi',
        'latitude'    => 'Lintang',
        'longitude'   => 'Bujur',
        'radius'      => 'Radius',
        'radius-help' => 'Jarak dalam meter dari titik ini. Contoh: 200 meter untuk satu site proyek. Salin lintang dan bujur dari peta.',
        'active'      => 'Dipakai untuk absen',
    ],

    'table' => [
        'name'        => 'Nama',
        'radius'      => 'Radius',
        'coordinates' => 'Titik',
        'active'      => 'Aktif',
    ],
];
