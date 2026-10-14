<?php

return [
    'model'  => 'Performance evaluation',
    'plural' => 'Performance evaluations',

    'navigation' => [
        'title' => 'Performance evaluation',
    ],

    'form' => [
        'section'          => 'Evaluation',
        'title'            => 'Title',
        'employee'         => 'Employee',
        'reviewer'         => 'Reviewer',
        'status'           => 'Status',
        'period-start'     => 'Period start',
        'period-end'       => 'Period end',
        'summary'          => 'Reviewer notes',
        'summary-helper'   => 'Overall notes for this period.',
        'employee-comment' => 'Employee response',
        'score-helper'     => 'Weighted average of criteria on a scale of 1 to 5.',
    ],

    'infolist' => [
        'section' => 'Evaluation',
    ],

    'table' => [
        'columns' => [
            'title'    => 'Title',
            'employee' => 'Employee',
            'reviewer' => 'Reviewer',
            'period'   => 'Period',
            'status'   => 'Status',
            'score'    => 'Score',
        ],
        'filters' => [
            'status'   => 'Status',
            'employee' => 'Employee',
        ],
        'empty' => [
            'heading'     => 'No evaluations yet',
            'description' => 'Create a performance evaluation for an employee.',
        ],
    ],

    'ratings' => [
        'title' => 'Criteria',
        'model' => 'Criterion',
        'form'  => [
            'title'         => 'Criterion',
            'weight'        => 'Weight',
            'weight-helper' => 'Weights do not need to total 100.',
            'score'         => 'Score',
            'score-helper'  => '1 is lowest, 5 is highest. Leave empty when not scored yet.',
            'comment'       => 'Comment',
        ],
        'empty' => [
            'heading'     => 'No criteria yet',
            'description' => 'Add the criteria that will be scored.',
        ],
    ],
];
