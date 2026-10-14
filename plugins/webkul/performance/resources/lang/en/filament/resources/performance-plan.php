<?php

return [
    'model'  => 'Performance plan',
    'plural' => 'Performance plans',

    'navigation' => [
        'title' => 'Performance',
        'plans' => 'Performance plans',
    ],

    'form' => [
        'section'        => 'Plan',
        'title'          => 'Title',
        'employee'       => 'Employee',
        'status'         => 'Status',
        'period-start'   => 'Period start',
        'period-end'     => 'Period end',
        'summary'        => 'Summary',
        'summary-helper' => 'Describe what this plan is meant to achieve.',
        'score-helper'   => 'Weighted average of goal progress. Weights do not have to total 100.',
    ],

    'infolist' => [
        'section' => 'Plan',
    ],

    'table' => [
        'columns' => [
            'title'    => 'Title',
            'employee' => 'Employee',
            'period'   => 'Period',
            'status'   => 'Status',
            'score'    => 'Score',
        ],
        'filters' => [
            'status'   => 'Status',
            'employee' => 'Employee',
        ],
        'empty' => [
            'heading'     => 'No performance plans yet',
            'description' => 'Create a plan for an employee, then add weighted goals.',
        ],
    ],

    'goals' => [
        'title' => 'Goals',
        'model' => 'Goal',
        'form'  => [
            'title'         => 'Goal',
            'metric'        => 'Metric',
            'target'        => 'Target',
            'weight'        => 'Weight',
            'weight-helper' => 'Relative weight used to calculate the plan score.',
            'progress'      => 'Progress',
            'status'        => 'Status',
            'notes'         => 'Notes',
        ],
        'empty' => [
            'heading'     => 'No goals yet',
            'description' => 'Add the individual targets that make up this plan.',
        ],
    ],
];
