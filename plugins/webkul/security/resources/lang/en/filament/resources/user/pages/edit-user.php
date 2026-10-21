<?php

return [
    'notification' => [
        'title'       => 'User updated',
        'body'        => 'The user has been updated successfully.',
        'error-title' => 'User could not be saved',
        'error-body'  => 'The form could not be saved. Try searching teams instead of listing all of them, then save again.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'Change Password',

            'notification' => [
                'title' => 'Password changed',
                'body'  => 'The password has been changed successfully.',
            ],

            'form' => [
                'new-password'         => 'New Password',
                'confirm-new-password' => 'Confirm New Password',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'User deleted',
                'body'  => 'The user has been deleted successfully.',
                'error' => [
                    'title' => 'User Cannot Be Deleted',
                    'body'  => 'This is a default user or you cannot delete yourself.',
                ],
            ],
        ],
    ],
];
