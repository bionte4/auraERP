<?php

namespace Webkul\Employee\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Employee\Services\ContractExpiryNotifier;

class NotifyExpiringContractsCommand extends Command
{
    protected $signature = 'employees:notify-expiring-contracts {--days=30 : Alert window in days before contract_end_date}';

    protected $description = 'Send database notifications for employee contracts expiring within the configured window';

    public function handle(ContractExpiryNotifier $notifier): int
    {
        $days = max(1, (int) $this->option('days'));

        $sent = $notifier->notifyExpiringContracts($days);

        $this->info("Sent {$sent} contract expiry notification(s).");

        return self::SUCCESS;
    }
}
