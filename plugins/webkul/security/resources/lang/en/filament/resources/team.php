<?php

return [
    'title' => 'Teams',

    'navigation' => [
        'title' => 'Teams',
    ],

    'form' => [
        'fields' => [
            'name'            => 'Name',
            'project'         => 'Linked project',
            'project-help'    => 'Optional explicit link. When set, team membership syncs from this project (preferred over name matching).',
            'members'         => 'Members (employees)',
            'members-help'    => 'Select employees, including those without a login account (marked No login). Saving assigns them to the linked project. Employees who have a Related User are also mirrored into team login membership for Group scope.',
            'members-search'  => 'Search employees…',
            'members-note'    => 'Employees without a login can still be assigned to the project. They cannot sign in until a Related User is created (Project → Assign Employees).',
            'no-login'        => 'No login',
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Name',
            'members'      => 'Members',
            'member-names' => 'Member names',
            'created-by'   => 'Created By',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Team updated',
                    'body'  => 'The team has been updated successfully.',
                ],
            ],

            'delete' => [
                'modal' => [
                    'heading'             => 'Delete team?',
                    'description'         => 'Team ":name" will be deleted. This cannot be undone.',
                    'description-members' => 'Team ":name" still has :count member(s). They will be removed from the team first, then the team will be deleted. This cannot be undone.',
                    'submit'              => 'Yes, delete team',
                ],
                'notification' => [
                    'title' => 'Team deleted',
                    'body'  => 'The team has been deleted successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Teams created',
                    'body'  => 'The teams has been created successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'    => 'Name',
            'members' => 'Team members',
        ],
    ],
];
