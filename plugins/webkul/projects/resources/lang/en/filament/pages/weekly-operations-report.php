<?php

return [
    'title'      => 'Weekly Operations Report',
    'subtitle'   => 'Snapshot for Operation Managers and HRD: leave, contracts, tasks, and assignment gaps.',
    'navigation' => [
        'label' => 'Weekly Ops Report',
    ],
    'actions' => [
        'download' => 'Download Excel',
    ],
    'table' => [
        'metric' => 'Metric',
        'value'  => 'Value',
        'notes'  => 'Notes',
    ],
    'load' => [
        'title'     => 'Workforce load (this week)',
        'subtitle'  => 'Active assigned employees: project count and timesheet hours this week.',
        'employee'  => 'Employee',
        'projects'  => 'Projects',
        'hours'     => 'Hours',
    ],
];
