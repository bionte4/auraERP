<?php

return [
    'errors' => [
        'unsupported-driver' => 'Database driver [:driver] is not supported for backup/restore.',
        'not-found'          => 'Backup file [:file] was not found.',
        'invalid-path'       => 'Invalid backup path.',
        'sqlite-missing'     => 'SQLite database file is missing or uses :memory:.',
        'write-failed'       => 'Unable to write backup file.',
        'decompress-failed'  => 'Unable to decompress backup archive.',
        'restore-disabled'   => 'Database restore is disabled in this environment. Download the backup and restore on a staging server.',
    ],

    'audit' => [
        'created'            => 'Database backup created: :file',
        'restored'           => 'Database restored from backup: :file',
        'deleted'            => 'Database backup deleted: :file',
        'restore-blocked'    => 'Database restore blocked for: :file',
        'employees-assigned' => 'Employees assigned on project :project (:count assigned, :without without related user)',
        'plugin-installed'   => 'Plugin installed: :plugin',
        'plugin-uninstalled' => 'Plugin uninstalled: :plugin',
    ],
];
