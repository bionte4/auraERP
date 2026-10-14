<?php

return [
    'header-actions' => [
        'template' => [
            'label' => 'Download Excel Template',
        ],

        'import' => [
            'label'             => 'Import Excel / CSV',
            'modal-heading'     => 'Bulk Import Equipment & Assets',
            'modal-description' => 'Upload an .xlsx or .csv file matching the template columns (name, serial_no, model, category, vendor, employee, project, customer, location, cost, effective_date, warranty_date, note). Existing serial_no records will be updated.',
            'file-label'        => 'Excel (.xlsx) or CSV (.csv) File',
            'notification'      => [
                'title' => 'Asset import completed',
                'body'  => 'Created :created new asset(s), updated :updated existing asset(s), skipped :skipped empty row(s).',
            ],
        ],

        'create' => [
            'label'        => 'New Equipment',
            'notification' => [
                'title' => 'Equipment created',
                'body'  => 'The equipment has been created successfully.',
            ],
        ],
    ],

    'tabs' => [
        'my-equipment'            => 'My Assets & Equipment',
        'employee-assets'         => 'Employee Assets',
        'project-customer-assets' => 'Project & Customer Assets',
        'assigned'                => 'Assigned',
        'unassigned'              => 'Unassigned',
        'under-maintenance'       => 'Under Maintenance',
        'archived'                => 'Archived',
    ],
];
