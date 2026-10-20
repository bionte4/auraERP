<?php

return [
    'navigation' => [
        'title' => 'Contract History',
    ],

    'fields' => [
        'employment-type' => 'Employment Type',
        'contract-number' => 'Contract Number',
        'start-date'      => 'Start Date',
        'end-date'        => 'End Date',
        'status'          => 'Status',
        'notes'           => 'Notes',
    ],

    'columns' => [
        'contract' => 'Contract',
        'duration' => 'Duration',
        'months'   => 'month(s)',
        'source'   => 'Source',
    ],

    'status' => [
        'active'     => 'Active',
        'completed'  => 'Completed',
        'extended'   => 'Extended',
        'converted'  => 'Converted to Permanent',
        'terminated' => 'Terminated',
    ],

    'actions' => [
        'create' => 'New Contract',
        'extend' => 'Extend Contract',
    ],
];
