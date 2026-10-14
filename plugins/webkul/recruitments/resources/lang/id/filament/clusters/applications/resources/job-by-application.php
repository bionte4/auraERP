<?php

return [
    'title' => 'Jabatan',

    'navigation' => [
        'title' => 'Jabatan',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Nama',
            'manager-name' => 'Manajer',
            'company-name' => 'Perusahaan',
        ],

        'actions' => [
            'applications' => [
                'new-applications' => ':count Lamaran Baru',
            ],

            'to-recruitment' => [
                'to-recruitment' => ':count ke Rekrutmen',
            ],

            'total-application' => [
                'total-application' => ':count Lamaran',
            ],
        ],
    ],

];
