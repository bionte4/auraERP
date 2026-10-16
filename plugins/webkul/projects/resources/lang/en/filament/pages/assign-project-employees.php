<?php

return [
    'navigation' => [
        'title' => 'Assign Employees',
    ],

    'title'       => 'Assign Employees to Project',
    'description' => 'Select a project, then choose the employees who should be members of that project. Assigned employees with a linked user account also gain project access as followers.',

    'fields' => [
        'project'           => 'Project',
        'employees'         => 'Employees',
        'employees-helper'  => 'Prefer employees that already have a Related User so they can log in and access the project.',
    ],

    'actions' => [
        'save'                   => 'Save Assignment',
        'provision-users'        => 'Create missing Related Users',
        'provision-heading'      => 'Create login users for assigned employees?',
        'provision-description'  => 'Creates Staff users from work email for employees assigned to projects but still missing a Related User. Temporary passwords are generated.',
    ],

    'notifications' => [
        'saved' => [
            'title'       => 'Employee assignment saved',
            'body'        => ':count employee(s) assigned to the project.',
            'body-warn'   => ':count employee(s) assigned. :without of them have no Related User yet — link a user account so they can access the project.',
        ],
        'provisioned' => [
            'title' => 'Related Users processed',
            'body'  => ':count created/linked, :skip skipped.',
        ],
        'missing-project' => [
            'title' => 'Please select a project first',
        ],
    ],

    'table' => [
        'heading'     => 'Currently assigned',
        'empty'       => 'No employees assigned to this project yet.',
        'name'        => 'Employee',
        'department'  => 'Department',
        'related-user'=> 'Related User',
        'none'        => '—',
    ],
];
