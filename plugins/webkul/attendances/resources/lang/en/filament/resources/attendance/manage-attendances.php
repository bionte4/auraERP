<?php

return [
    'header-actions' => [
        'create' => [
            'label' => 'Record attendance',
        ],
        'clock-in' => [
            'label' => 'Clock in',
        ],
        'clock-out' => [
            'label' => 'Clock out',
        ],
    ],

    'tabs' => [
        'today' => 'Today',
        'mine'  => 'My attendance',
    ],

    'notifications' => [
        'clock-in' => [
            'title' => 'Clock in recorded',
            'body'  => 'Your arrival for today has been saved.',
        ],
        'clock-out' => [
            'title' => 'Clock out recorded',
            'body'  => 'Your departure for today has been saved.',
        ],
        'already-in' => [
            'title' => 'Already clocked in',
            'body'  => 'You already have a clock-in for today.',
        ],
        'missing-open' => [
            'title' => 'No open clock-in',
            'body'  => 'Clock in first, or today is already closed.',
        ],
        'missing-employee' => [
            'title' => 'Employee record not found',
            'body'  => 'Your account is not linked to an employee. Record attendance from the form, or link this user on the employee.',
        ],
    ],
];
