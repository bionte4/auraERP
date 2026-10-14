<?php

return [
    'tabs' => [
        'my-team'            => 'Tim Saya',
        'my-department'      => 'Departemen Saya',
        'archived'           => 'Diarsipkan',
        'newly-hired'        => 'Baru Diterima',
        'expiring-contracts' => 'Kontrak Akan Berakhir',
    ],

    'header-actions' => [
        'template' => [
            'label' => 'Unduh Template Excel',
        ],

        'import' => [
            'label'             => 'Import Excel / CSV',
            'modal-heading'     => 'Bulk Import Karyawan',
            'modal-description' => 'Unggah file .xlsx atau .csv sesuai format template (kolom: name, work_email, work_phone, mobile_phone, job_title, department, employment_type, join_date, contract_end_date). Jika work_email sudah ada, data karyawan akan diperbarui.',
            'file-label'        => 'File Excel (.xlsx) atau CSV (.csv)',
            'notification'      => [
                'title' => 'Import karyawan selesai',
                'body'  => 'Berhasil menambahkan :created karyawan baru, memperbarui :updated karyawan, dan melewati :skipped baris kosong.',
            ],
        ],

        'create' => [
            'label' => 'Karyawan Baru',
        ],
    ],
];
