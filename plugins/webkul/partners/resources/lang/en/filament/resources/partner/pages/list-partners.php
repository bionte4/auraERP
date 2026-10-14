<?php

return [
    'title' => 'Contacts',

    'header-actions' => [
        'template' => [
            'label' => 'Download Excel Template',
        ],

        'import' => [
            'label'             => 'Import Excel / CSV',
            'modal-heading'     => 'Bulk Import Customers & Vendors (Contacts)',
            'modal-description' => 'Upload an .xlsx or .csv file matching the template columns (name, role [customer/vendor/both], account_type [company/individual], email, phone, mobile, tax_id, website, street1, city, zip, reference).',
            'file-label'        => 'Excel (.xlsx) or CSV (.csv) File',
            'notification'      => [
                'title' => 'Contact import completed',
                'body'  => 'Created :created new contact(s), updated :updated existing contact(s), skipped :skipped empty row(s).',
            ],
        ],

        'create' => [
            'label' => 'Create Contact',
        ],
    ],

    'tabs' => [
        'individuals' => 'Individuals',
        'companies'   => 'Companies',
        'employees'   => 'Employees',
        'customers'   => 'Customers',
        'vendors'     => 'Vendors',
        'archived'    => 'Archived',
    ],
];
