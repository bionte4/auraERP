<?php

namespace Webkul\Security\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Security\Services\RolePermissionSynchronizer;

class SyncRbacPermissionsCommand extends Command
{
    protected $signature = 'security:sync-rbac {--shield : Also invoke shield:generate before syncing}';

    protected $description = 'Synchronize all RBAC permissions for Admin, HRD, Kasir (POS), and Staff roles';

    public function handle(RolePermissionSynchronizer $synchronizer): int
    {
        $this->info('Synchronizing auraERP RBAC roles & permissions...');

        $result = $synchronizer->syncAll((bool) $this->option('shield'));

        $this->info(sprintf(
            'Synced %d permissions across roles: %s',
            $result['permissions_count'],
            implode(', ', $result['roles_synced'])
        ));

        return self::SUCCESS;
    }
}
