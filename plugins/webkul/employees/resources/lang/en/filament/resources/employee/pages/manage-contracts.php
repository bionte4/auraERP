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
        'status-helper-text' => 'Active = current contract (only one may be active; activating a newer contract automatically marks the previous one as Extended). Completed = ended normally. Converted to Permanent = changed to PKWTT. Terminated = stopped before its end date.',
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
