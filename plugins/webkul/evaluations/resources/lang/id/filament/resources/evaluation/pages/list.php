<?php

return [
    'header-actions' => [
        'create' => 'Evaluasi baru',

        'template' => [
            'label' => 'Unduh template import',
        ],

        'import' => [
            'label'             => 'Import Excel / CSV',
            'modal-heading'     => 'Bulk import evaluasi kinerja',
            'modal-description' => 'Unggah file .xlsx atau .csv sesuai template. Karyawan dicocokkan lewat NIK (employee_nik) atau nama persis. Nilai aspek pakai score_1…score_10 (skala 1–5). Baris dengan karyawan + tipe + periode yang sama akan diperbarui; evaluasi yang sudah selesai dilewati.',
            'file-label'        => 'File Excel (.xlsx) atau CSV (.csv)',
            'notification'      => [
                'title' => 'Import evaluasi selesai',
                'body'  => 'Berhasil menambahkan :created, memperbarui :updated, dan melewati :skipped baris.',
            ],
        ],
    ],
];
