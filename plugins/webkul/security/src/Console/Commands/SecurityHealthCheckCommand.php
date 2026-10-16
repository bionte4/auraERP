<?php

namespace Webkul\Security\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Security\Services\DatabaseBackupManager;

class SecurityHealthCheckCommand extends Command
{
    protected $signature = 'security:health-check';

    protected $description = 'Check backup health and related security operations signals';

    public function handle(DatabaseBackupManager $backups): int
    {
        $health = $backups->health();

        $this->table(
            ['Metric', 'Value'],
            [
                ['OK', ($health['ok'] ? 'yes' : 'no')],
                ['Last backup', $health['last_backup_at'] ?? '—'],
                ['Age (hours)', $health['age_hours'] ?? '—'],
                ['Backup count', (string) $health['count']],
                ['Restore allowed', $health['restore_allowed'] ? 'yes' : 'no'],
                ['Disk free (MB)', $health['disk_free_mb'] ?? '—'],
            ]
        );

        return $health['ok'] ? self::SUCCESS : self::FAILURE;
    }
}
