<?php

return [
    'navigation' => [
        'title' => 'Project Budget Control',
    ],

    'title'       => 'Project Budget Control',
    'description' => 'Contract value vs invoicing and operational budget absorption per project. Realization comes from posted invoices and vendor bills linked to the project; link documents via the Project field on the invoice/bill form.',

    'summary' => [
        'contract'    => 'Total contract value',
        'invoiced'    => 'Total invoiced',
        'unpaid'      => 'Total unpaid',
        'vendor-cost' => 'Total vendor cost',
        'attention'   => 'Projects needing attention',
    ],

    'status' => [
        'all'       => 'All',
        'ok'        => 'Safe',
        'warning'   => 'Warning ≥80%',
        'over'      => 'Over budget',
        'no_budget' => 'No budget set',
    ],

    'search-placeholder' => 'Search projects or customers…',

    'table' => [
        'project'     => 'Project',
        'customer'    => 'Customer',
        'contract'    => 'Contract Value',
        'invoiced'    => 'Invoiced',
        'unpaid'      => 'Unpaid',
        'budget'      => 'Operational Budget',
        'vendor-cost' => 'Vendor Cost (actual)',
        'absorption'  => 'Absorption',
        'hours'       => 'Timesheet Hours',
        'empty'       => 'No projects match the current filter.',
    ],

    'note' => 'Absorption = posted vendor bills linked to the project ÷ operational budget on the project. Documents not yet linked to a project are not counted — link them from the invoice/bill form.',

    'actions' => [
        'export'      => 'Export Excel',
        'set-budget'  => 'Set budget',
        'edit-budget' => 'Edit',
        'save'        => 'Save',
        'cancel'      => 'Cancel',
    ],

    'notifications' => [
        'budget-saved' => 'Operational budget updated.',
    ],
];
