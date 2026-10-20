<?php

return [
    'tabs' => [
        'my-team'            => 'My Team',
        'my-department'      => 'My Department',
        'archived'           => 'Archived',
        'newly-hired'        => 'Newly Hired',
        'expiring-contracts' => 'Expiring Contracts',
    ],

    'header-actions' => [
        'template' => [
            'label' => 'Download Excel Template',
        ],

        'import' => [
            'label'             => 'Import Excel / CSV',
            'modal-heading'     => 'Bulk Import Employees',
            'modal-description' => 'Upload an .xlsx or .csv file matching the template columns (name, work_email, work_phone, mobile_phone, job_title, department, employment_type, join_date, contract_end_date). Existing work_email records will be updated.',
            'file-label'        => 'Excel (.xlsx) or CSV (.csv) File',
            'notification'      => [
                'title' => 'Employee import completed',
                'body'  => 'Created :created new employee(s), updated :updated existing employee(s), skipped :skipped empty row(s).',
            ],
                'unknown-types' => [
                    'title' => 'Unknown employment types skipped',
                    'body'  => 'These employment type names from the file do not exist yet and were left empty: :types. Create them first in Configurations → Employment Types, then re-import or set them manually.',
                ],
        ],

        'create' => [
            'label' => 'New Employee',
        ],
    ],
];
