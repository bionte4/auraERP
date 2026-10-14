<?php

return [
    'model'  => 'Attendance',
    'plural' => 'Attendance',

    'navigation' => [
        'title' => 'Attendance',
    ],

    'form' => [
        'section'         => 'Attendance',
        'employee'        => 'Employee',
        'date'            => 'Work date',
        'status'          => 'Status',
        'check-in'        => 'Clock in',
        'check-out'       => 'Clock out',
        'note'            => 'Note',
        'late-helper'     => 'Clock in after 09:00 is marked as late.',
        'check-out-after' => 'Clock out must be after clock in.',
    ],

    'table' => [
        'columns' => [
            'date'               => 'Date',
            'employee'           => 'Employee',
            'check-in'           => 'Clock in',
            'check-out'          => 'Clock out',
            'check-in-location'  => 'Clock-in location',
            'check-out-location' => 'Clock-out location',
            'worked'             => 'Duration',
            'tracks'             => 'GPS trail',
            'status'             => 'Status',
            'note'               => 'Note',
        ],
        'groups' => [
            'date'     => 'Date',
            'employee' => 'Employee',
        ],
        'filters' => [
            'status'   => 'Status',
            'employee' => 'Employee',
            'from'     => 'From date',
            'until'    => 'Until date',
        ],
        'empty' => [
            'heading'     => 'No attendance yet',
            'description' => 'Clock in for today, or record attendance for an employee.',
        ],
    ],

    'tracks' => [
        'action'  => 'Trail',
        'heading' => 'Location trail',
        'empty'   => 'No location points yet.',
    ],
];
