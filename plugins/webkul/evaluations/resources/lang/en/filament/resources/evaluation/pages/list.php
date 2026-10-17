<?php

return [
    'header-actions' => [
        'create' => 'New evaluation',

        'template' => [
            'label' => 'Download import template',
        ],

        'import' => [
            'label'             => 'Import Excel / CSV',
            'modal-heading'     => 'Bulk import evaluations',
            'modal-description' => 'Upload an .xlsx or .csv file matching the template. Match employees by NIK (employee_nik) or exact name. Scores use score_1…score_10 (scale 1–5). Rows with the same employee + evaluation_type + period are updated; completed evaluations are skipped.',
            'file-label'        => 'Excel (.xlsx) or CSV (.csv) file',
            'notification'      => [
                'title' => 'Evaluation import completed',
                'body'  => 'Created :created, updated :updated, skipped :skipped row(s).',
            ],
        ],
    ],
];
