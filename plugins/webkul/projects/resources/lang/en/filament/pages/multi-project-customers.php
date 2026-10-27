<?php

return [
    'navigation' => [
        'title'         => 'Multi-project customers',
        'badge-tooltip' => 'Customers with two or more active projects',
    ],
    'title'       => 'Customers with multiple projects',
    'description' => 'Customers that currently have two or more active Customer Projects. One client can hold several contracts.',
    'columns'     => [
        'customer'    => 'Customer',
        'projects'    => 'Projects',
        'total-value' => 'Total value',
        'project-list'=> 'Project names',
    ],
    'actions' => [
        'view-customer' => 'View customer',
        'view-projects' => 'View projects',
    ],
    'empty' => [
        'heading'     => 'No multi-project customers',
        'description' => 'No customer currently has more than one active project.',
    ],
];
