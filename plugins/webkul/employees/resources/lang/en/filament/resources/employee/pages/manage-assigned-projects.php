<?php

return [
    'navigation' => [
        'title' => 'Assigned Projects',
    ],

    'title' => 'Assigned Projects — :name',

    'section' => [
        'title'       => 'Project assignment',
        'description' => 'Select the projects this employee should belong to. Access is synced via project followers when a Related User/partner exists.',
    ],

    'fields' => [
        'projects'         => 'Projects',
        'projects-helper'  => 'You can also assign employees from Project → Assign Employees.',
    ],

    'actions' => [
        'save' => 'Save Assignment',
    ],

    'notifications' => [
        'saved' => [
            'title'     => 'Project assignment saved',
            'body'      => ':count project(s) assigned to this employee.',
            'body-warn' => ':count project(s) assigned. This employee has no Related User yet — link a user account so they can access the projects.',
        ],
    ],

    'table' => [
        'heading'  => 'Currently assigned',
        'empty'    => 'No projects assigned to this employee yet.',
        'project'  => 'Project',
        'customer' => 'Customer',
        'manager'  => 'Project Manager',
        'none'     => '—',
    ],
];
