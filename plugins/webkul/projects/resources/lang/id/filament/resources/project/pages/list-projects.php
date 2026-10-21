<?php

return [
    'tabs' => [
        'my-projects'          => 'Proyek Saya',
        'customer-projects'    => 'Proyek Pelanggan',
        'expiring-soon'        => 'Berakhir ≤ 3 Bulan',
        'my-favorite-projects' => 'Favorit Saya',
        'unassigned-projects'  => 'Proyek Belum Ditugaskan',
        'archived-projects'    => 'Proyek Diarsipkan',
    ],

    'header-actions' => [
        'template' => [
            'label' => 'Template Impor Proyek',
        ],

        'import' => [
            'label'             => 'Impor Proyek Excel / CSV',
            'modal-heading'     => 'Bulk Import Proyek',
            'modal-description' => 'Unggah file .xlsx atau .csv sesuai format template (kolom: name, customer, project_manager, stage, start_date, end_date, project_value, operational_cost, allocated_hours, visibility, description). Satu customer dapat memiliki banyak baris proyek.',
            'file-label'        => 'File Excel (.xlsx) atau CSV (.csv)',
            'notification'      => [
                'title' => 'Impor proyek selesai',
                'body'  => 'Berhasil menambahkan :created proyek baru, memperbarui :updated proyek, dan melewati :skipped baris.',
            ],
        ],

        'create' => [
            'label' => 'Proyek Baru',
        ],
    ],
];
