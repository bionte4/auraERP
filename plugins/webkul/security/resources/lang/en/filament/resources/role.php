<?php

return [
    'form' => [
        'fields' => [
            'web'     => 'Web',
            'sanctum' => 'Sanctum',
        ],
    ],

    'notification' => [
        'system-role-delete' => [
            'title' => 'System Role Cannot Be Deleted',
            'body'  => 'This is a system role and cannot be deleted.',
        ],

        'baseline-role-delete' => [
            'title' => 'Baseline Role Cannot Be Deleted',
            'body'  => 'This role is part of the security:sync-rbac baseline. Edit its permissions instead of deleting it.',
        ],

        'role-in-use-delete' => [
            'title' => 'Role Is Still Assigned',
            'body'  => 'This role is assigned to :count user(s). Reassign them to another role first, then delete this role.',
        ],

        'delete-role' => [
            'title'       => 'Delete Role :name',
            'description' => 'Guard: :guard · :permissions permissions · :users user(s) assigned. This action cannot be undone.',
        ],
    ],
];
