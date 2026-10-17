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
        'start_date'    => 'Start date',
        'end_date'      => 'End date',
        'notes'         => 'Notes for customer',
        'notes-helper'  => 'Optional message included on the Excel cover sheet.',
    ],
    'actions' => [
        'preview'  => 'Preview report',
        'download' => 'Download Excel',
    ],
    'notifications' => [
        'missing-project'  => 'Please select a project first',
        'missing-dates'    => 'Please set both start and end dates',
        'forbidden'        => 'You cannot report on this project',
        'preview-ready'    => 'Preview is ready — review below, then download Excel',
        'preview-required' => 'Preview the report first, then download Excel',
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
        'completed'      => 'Completed in period (masked)',
        'open'           => 'In progress (masked)',
        'empty'          => 'No items in this section.',
        'masked-hint'    => 'Real task names and “overdue” wording are hidden for client sharing.',
        'select-project' => 'Select a project and date range, click Preview report, review the data, then Download Excel.',
    ],
];
