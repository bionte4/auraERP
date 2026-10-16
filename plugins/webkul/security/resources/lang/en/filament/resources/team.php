<?php

return [
    'title' => 'Teams',

    'navigation' => [
        'title' => 'Teams',
    ],

    'form' => [
        'fields' => [
            'name'          => 'Name',
            'members'       => 'Team members',
            'members-help'  => 'Select users who belong to this team. Manage Teams counts projects where these users are Project Manager.',
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
