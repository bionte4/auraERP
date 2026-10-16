<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Periodic database backup
    |--------------------------------------------------------------------------
    */
    'enabled' => (bool) env('SECURITY_BACKUP_ENABLED', true),

    /*
    | Absolute or storage-relative directory for backup files.
    | Default: storage/app/private/database-backups
    */
    'path' => env('SECURITY_BACKUP_PATH', storage_path('app/private/database-backups')),

    /*
    | Keep backups for this many days (cleanup command / prune).
    */
    'retention_days' => (int) env('SECURITY_BACKUP_RETENTION_DAYS', 14),

    /*
    | Maximum number of backup files to keep (0 = unlimited by count).
    */
    'retention_count' => (int) env('SECURITY_BACKUP_RETENTION_COUNT', 30),

    /*
    | Daily schedule time (24h, server timezone).
    */
    'schedule_at' => env('SECURITY_BACKUP_SCHEDULE_AT', '02:00'),

    /*
    | Compress SQL dumps with gzip when possible.
    */
    'compress' => (bool) env('SECURITY_BACKUP_COMPRESS', true),

    /*
    | Prefer native tools (mysqldump / pg_dump) when available.
    */
    'prefer_native' => (bool) env('SECURITY_BACKUP_PREFER_NATIVE', true),

    /*
    | Phrase the Admin must type to confirm a destructive restore.
    */
    'restore_confirmation' => env('SECURITY_BACKUP_RESTORE_CONFIRMATION', 'RESTORE'),

    /*
    | Allow UI/CLI restore. When null, restore is disabled in the "production"
    | environment and enabled elsewhere. Set explicitly with true/false to override.
    */
    'allow_restore' => env('SECURITY_BACKUP_ALLOW_RESTORE'),
];
