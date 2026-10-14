<?php

return [
    'tabs' => [
        'my-projects'          => 'My Projects',
        'customer-projects'    => 'Customer Projects',
        'expiring-soon'        => 'Ending ≤ 3 Months',
        'my-favorite-projects' => 'My Favorites',
        'unassigned-projects'  => 'Unassigned Projects',
        'archived-projects'    => 'Archived Projects',
    ],

    'header-actions' => [
        'template' => [
            'label' => 'Download Template',
        ],

        'import' => [
            'label'             => 'Import Excel / CSV',
            'modal-heading'     => 'Bulk Import Projects',
            'modal-description' => 'Upload an .xlsx or .csv file matching the template columns (name, customer, project_manager, stage, start_date, end_date, project_value, allocated_hours, visibility, description). One customer can have multiple project rows.',
            'file-label'        => 'Excel (.xlsx) or CSV (.csv) File',
            'notification'      => [
                'title' => 'Project import completed',
                'body'  => 'Created :created project(s), updated :updated project(s), and skipped :skipped row(s).',
            ],
        ],

        'create' => [
            'label' => 'New Project',
        ],
    ],
];
