<?php

return [
    'heading' => [
        'title' => 'Contracts Approaching / Past End Date',
    ],

    'table-columns' => [
        'employee'        => 'Employee',
        'department'      => 'Department',
        'job'             => 'Job Position',
        'employment-type' => 'Employment Type',
        'manager'         => 'Manager',
        'contract-end'    => 'Contract End',
        'status'          => 'Status',
        'projects'        => 'Projects',
    ],

    'status' => [
        'overdue'    => 'Ended :days day(s) ago',
        'ends-today' => 'Ends today',
        'days-left'  => ':days day(s) left',
    ],

    'filters' => [
        'window' => [
            'label'     => 'Contract window',
            'all'       => 'All (ended + next 3 months)',
            'expired'   => 'Already ended',
            'within-7'  => 'Within 7 days',
            'within-30' => 'Within 30 days',
            '31-90'     => '31–90 days',
        ],
    ],

    'empty' => [
        'heading'     => 'No contracts approaching their end date',
        'description' => 'Active employees whose contract ends within the next 3 months (or already ended) will appear here.',
    ],
];
