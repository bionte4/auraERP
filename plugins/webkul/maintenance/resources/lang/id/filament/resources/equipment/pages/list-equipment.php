<?php

return [
    'header-actions' => [
        'template' => [
            'label' => 'Unduh Template Excel',
        ],

        'import' => [
            'label'             => 'Import Excel / CSV',
            'modal-heading'     => 'Bulk Import Aset & Peralatan',
            'modal-description' => 'Unggah file .xlsx atau .csv sesuai format template (kolom: name, serial_no, model, category, vendor, employee, project, customer, location, cost, effective_date, warranty_date, note). Jika serial_no sudah ada, data aset akan diperbarui.',
            'file-label'        => 'File Excel (.xlsx) atau CSV (.csv)',
            'notification'      => [
                'title' => 'Import aset selesai',
                'body'  => 'Berhasil menambahkan :created aset baru, memperbarui :updated aset, dan melewati :skipped baris kosong.',
            ],
        ],

        'create' => [
            'label'        => 'Peralatan Baru',
            'notification' => [
                'title' => 'Peralatan dibuat',
                'body'  => 'Peralatan berhasil dibuat.',
            ],
        ],
    ],

    'tabs' => [
        'my-equipment'            => 'Aset & Peralatan Saya',
        'employee-assets'         => 'Aset Karyawan',
        'project-customer-assets' => 'Aset Proyek & Customer',
        'assigned'                => 'Ditugaskan',
        'unassigned'              => 'Belum Ditugaskan',
        'under-maintenance'       => 'Dalam Pemeliharaan',
        'archived'                => 'Diarsipkan',
    ],
];
