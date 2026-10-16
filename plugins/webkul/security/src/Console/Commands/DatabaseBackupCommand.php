<?php

namespace Webkul\Security\Console\Commands;

use Illuminate\Console\Command;
use Throwable;
use Webkul\Security\Services\DatabaseBackupManager;

class DatabaseBackupCommand extends Command
{
    protected $signature = 'security:database-backup {--note= : Optional note stored with the backup}';

    protected $description = 'Create a periodic/manual database backup';

    public function handle(DatabaseBackupManager $backups): int
    {
        if (! config('security-backup.enabled', true)) {
            $this->warn('Database backup is disabled (SECURITY_BACKUP_ENABLED=false).');

            return self::SUCCESS;
        }

        try {
            $meta = $backups->create($this->option('note'));
            $this->info(sprintf(
                'Backup created: %s (%s) via %s',
                $meta['filename'],
                number_format($meta['size']).' bytes',
                $meta['method'],
            ));

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
