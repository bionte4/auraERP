<?php

return [
    'title' => 'Teams',

    'navigation' => [
        'title' => 'Teams',
    ],

    'form' => [
        'fields' => [
            'name'          => 'Name',
            'project'       => 'Linked project',
            'project-help'  => 'Optional explicit link. When set, team membership syncs from this project (preferred over name matching).',
            'members'       => 'Team members',
            'members-help'  => 'Members are user (login) accounts, not employee records. For a project-linked team, accounts backed by an employee record mirror the project roster automatically (Project Manager + assigned employees): unassigned employees are removed again on sync, while accounts without an employee record stay. Assign employees via Project → Assign Employees.',
            'unsynced'      => 'Project employees who cannot sync (no Related User / login account)',
            'unsynced-help' => 'These employees are assigned to the linked project but have no login account, so they cannot become team members. Create their Related User from Project → Assign Employees to include them.',
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
                    'heading'            => 'Delete team?',
                    'description'        => 'Team ":name" will be deleted. This cannot be undone.',
                    'description-members' => 'Team ":name" still has :count member(s). They will be removed from the team first, then the team will be deleted. This cannot be undone.',
                    'submit'             => 'Yes, delete team',
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
