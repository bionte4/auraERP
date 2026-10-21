<?php

return [
    'navigation' => [
        'title'         => 'Assign Project Managers',
        'badge-tooltip' => 'Projects without a Project Manager',
    ],

    'title'       => 'Assign Project Managers',
    'description' => 'Set the Project Manager (Operation Manager) for every project in one grid. Changes are saved immediately and the new manager is synced into the project team. Only users with the Operation Manager role can be chosen.',

    'summary' => [
        'all'        => 'All projects',
        'unassigned' => 'No manager yet',
    ],

    'search-placeholder' => 'Search projects…',

    'table' => [
        'project'    => 'Project',
        'customer'   => 'Customer',
        'members'    => 'Members',
        'end-date'   => 'End Date',
        'manager'    => 'Project Manager',
        'no-manager' => 'No manager yet',
        'empty'      => 'No projects match the current filter.',
    ],

    'note' => 'Clearing or replacing a manager does not remove the previous manager from the team members — remove them manually in Teams if needed.',

    'actions' => [
        'template'           => 'Project Manager Template',
        'import'             => 'Import Excel / CSV',
        'import-heading'     => 'Bulk assign Project Managers',
        'import-description' => 'Download the template first: it is prefilled with every project and its current manager email. Edit the new_manager_email column (leave empty to clear a manager), then upload the file back. Projects are matched by project_id, managers by email, and only Operation Managers are accepted. The second sheet lists valid manager emails.',
        'import-file'        => 'Template file (.xlsx / .csv)',
    ],

    'notifications' => [
        'saved' => [
            'title'       => 'Project Manager saved',
            'body'        => ':manager now manages ":project".',
            'body-cleared' => 'Project Manager cleared for ":project".',
        ],
        'invalid-manager' => [
            'title' => 'Selected user is not an Operation Manager',
        ],
        'imported' => [
            'title' => 'Project Manager import finished',
            'body'  => ':updated updated, :unchanged unchanged, :skipped skipped.',
        ],
    ],
];
