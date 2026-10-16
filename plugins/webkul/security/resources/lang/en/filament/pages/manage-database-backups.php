<?php

return [
    'title' => 'Database Backups',

    'navigation' => [
        'label' => 'Database Backups',
    ],

    'info' => [
        'heading'   => 'Periodic backup & restore',
        'enabled'   => 'Scheduler: :status',
        'on'        => 'enabled',
        'off'       => 'disabled',
        'schedule'  => 'Automatic backup runs daily at :time (server timezone).',
        'retention' => 'Retention: :days day(s), max :count file(s).',
        'directory' => 'Storage path: :path',
        'cron'      => 'Ensure the Laravel scheduler cron is active: * * * * * php /path/to/artisan schedule:run',
    ],

    'table' => [
        'filename'   => 'File',
        'created-at' => 'Created at',
        'size'       => 'Size',
        'method'     => 'Method / driver',
        'actions'    => 'Actions',
        'empty'      => 'No backups yet. Create one manually or wait for the daily schedule.',
    ],

    'actions' => [
        'create'                 => 'Create backup now',
        'create-heading'         => 'Create database backup?',
        'create-description'     => 'This will dump the current database into a backup file on the server.',
        'prune'                  => 'Prune old backups',
        'download'               => 'Download',
        'restore'                => 'Restore',
        'restore-heading'        => 'Restore database from backup?',
        'restore-description'    => 'This overwrites the live database. Type :phrase to confirm.',
        'restore-confirm-label'  => 'Type :phrase to confirm',
        'restore-preconfirm'     => 'Continue to restore confirmation?',
        'delete'                 => 'Delete',
        'delete-confirm'         => 'Delete this backup file?',
    ],

    'notifications' => [
        'created' => [
            'title' => 'Backup created',
            'body'  => 'File: :file',
        ],
        'restored' => [
            'title' => 'Database restored',
            'body'  => 'Restored from: :file',
        ],
        'deleted' => [
            'title' => 'Backup deleted',
        ],
        'pruned' => [
            'title' => 'Old backups pruned',
            'body'  => ':count file(s) removed.',
        ],
        'failed' => [
            'title' => 'Backup operation failed',
        ],
    ],
];
