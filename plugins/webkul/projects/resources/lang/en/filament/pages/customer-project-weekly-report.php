<?php

return [
    'title'      => 'Customer Project Weekly Report',
    'subtitle'   => 'Client-ready weekly project progress. Task titles and schedule pressure are masked; timesheet hours are omitted.',
    'navigation' => [
        'label' => 'Customer Project Report',
    ],
    'form' => [
        'section' => 'Select project',
    ],
    'fields' => [
        'project'       => 'Project',
        'notes'         => 'Notes for customer',
        'notes-helper'  => 'Optional message included on the Excel cover sheet.',
    ],
    'actions' => [
        'download' => 'Download Excel',
    ],
    'notifications' => [
        'missing-project' => 'Please select a project first',
        'forbidden'       => 'You cannot report on this project',
    ],
    'table' => [
        'metric' => 'Metric',
        'value'  => 'Value',
        'notes'  => 'Notes',
        'item'   => 'Item',
        'status' => 'Status',
        'date'   => 'Date',
    ],
    'preview' => [
        'cover'          => 'Report preview',
        'notes'          => 'Notes for customer',
        'completed'      => 'Completed this week (masked)',
        'open'           => 'In progress (masked)',
        'empty'          => 'No items in this section.',
        'masked-hint'    => 'Real task names and “overdue” wording are hidden for client sharing.',
        'select-project' => 'Select a project to preview the customer report.',
    ],
];
