<?php

return [
    'title'      => 'Weekly Operations Report',
    'subtitle'   => 'Actionable snapshot for Operation Managers and HRD: leave, contracts, tasks, and assignment gaps.',
    'navigation' => [
        'label' => 'Weekly Ops Report',
    ],
    'actions' => [
        'download' => 'Download Excel',
    ],
    'snapshot' => [
        'title' => 'Operational snapshot',
    ],
    'table' => [
        'metric' => 'Metric',
        'value'  => 'Value',
        'notes'  => 'Notes',
    ],
    'load' => [
        'title'     => 'Workforce load (this week)',
        'subtitle'  => 'Top assigned employees by timesheet hours this week (within your scope).',
        'employee'  => 'Employee',
        'projects'  => 'Projects',
        'hours'     => 'Hours',
        'empty'     => 'No assigned employees in your scope this week.',
    ],
];
