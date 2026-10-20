<?php

return [
    'heading' => [
        'title' => 'Needs Attention',
    ],

    'table-columns' => [
        'employee'   => 'Employee',
        'department' => 'Department',
        'manager'    => 'Manager',
        'issues'     => 'Issues',
        'projects'   => 'Projects',
        'join-date'  => 'Join Date',
    ],

    'issues' => [
        'no-user'       => 'No Related User',
        'no-department' => 'No department',
        'no-job'        => 'No job position',
        'no-manager'    => 'No manager',
        'no-email'      => 'No email',
        'no-project'    => 'No project',
    ],

    'filters' => [
        'issue' => [
            'label'      => 'Issue',
            'all'        => 'All issues',
            'no-user'    => 'Without Related User',
            'incomplete' => 'Incomplete profile',
            'no-project' => 'Without project',
        ],
    ],

    'empty' => [
        'heading'     => 'No employees need attention',
        'description' => 'Active employees missing a login, profile basics, or a project assignment will appear here.',
    ],
];
