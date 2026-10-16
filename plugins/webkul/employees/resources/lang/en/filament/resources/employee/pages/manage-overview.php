<?php

return [
    'navigation' => [
        'label' => 'Overview 360',
    ],
    'title'   => 'Overview — :name',
    'actions' => [
        'provision' => 'Create Related User',
    ],
    'cards' => [
        'department'   => 'Department',
        'related-user' => 'Related User',
        'capacity'     => 'Capacity this week',
        'contract'     => 'Contract end',
        'leave'        => 'Leave requests (YTD)',
        'assets'       => 'Assets held',
        'evaluation'   => 'Latest evaluation',
    ],
    'sections' => [
        'projects' => 'Assigned projects',
    ],
    'provision' => [
        'errors' => [
            'email'       => 'Employee needs a valid work email before a user can be created.',
            'email-taken' => 'That email is already linked to another employee.',
        ],
    ],
    'notifications' => [
        'provisioned' => [
            'title' => 'Related User ready',
            'body'  => 'Email: :email — temporary password: :password',
        ],
    ],
];
