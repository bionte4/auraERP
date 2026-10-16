<?php

namespace Webkul\Security\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Security\Services\DatabaseBackupManager;

class DatabaseBackupCleanCommand extends Command
{
    protected $signature = 'security:database-backup-clean';

    protected $description = 'Prune old database backups by retention policy';

    public function handle(DatabaseBackupManager $backups): int
    {
        $deleted = $backups->prune();

        $this->info(sprintf('Pruned %d backup file(s).', $deleted));

        return self::SUCCESS;
    }
}
