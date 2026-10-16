<?php

return [
    'title'  => 'Audit Trail',
    'plural' => 'Audit Trails',

    'navigation' => [
        'title' => 'Audit Trail',
    ],

    'actions' => [
        'back' => 'Back to list',
    ],

    'table' => [
        'columns' => [
            'occurred-at'  => 'Occurred at',
            'event'        => 'Event',
            'actor'        => 'Actor',
            'subject'      => 'Subject',
            'description'  => 'Description',
            'ip'           => 'IP address',
            'guard'        => 'Guard',
            'user-agent'   => 'User agent',
            'properties'   => 'Details',
        ],
        'filters' => [
            'event' => 'Event',
        ],
    ],

    'infolist' => [
        'sections' => [
            'overview' => 'Security access details',
        ],
    ],
];
