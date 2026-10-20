<?php

return [
    'heading' => [
        'title' => 'Projects Approaching / Past Deadline',
    ],

    'table-columns' => [
        'project'         => 'Project',
        'customer'        => 'Customer',
        'project-manager' => 'Project Manager',
        'end-date'        => 'End Date',
        'status'          => 'Status',
        'progress'        => 'Task Progress',
        'overdue-tasks'   => 'Overdue Tasks',
    ],

    'status' => [
        'overdue'    => 'Overdue by :days day(s)',
        'ends-today' => 'Ends today',
        'days-left'  => ':days day(s) left',
    ],

    'filters' => [
        'window' => [
            'label'     => 'Deadline window',
            'all'       => 'All (expired + next 3 months)',
            'expired'   => 'Expired',
            'within-30' => 'Within 30 days',
            '31-60'     => '31–60 days',
            '61-90'     => '61–90 days',
        ],
    ],

    'empty' => [
        'heading'     => 'No projects approaching their deadline',
        'description' => 'Active projects ending within the next 3 months (or already past their end date) will appear here.',
    ],
];
