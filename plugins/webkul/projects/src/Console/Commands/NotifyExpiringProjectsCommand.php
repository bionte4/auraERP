<?php

namespace Webkul\Project\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Project\Services\ProjectExpiryNotifier;

class NotifyExpiringProjectsCommand extends Command
{
    protected $signature = 'projects:notify-expiring-projects {--days=90 : Alert window in days before project end_date (3, 2, 1 months)}';

    protected $description = 'Send tiered 3, 2, and 1 month database notifications for projects approaching their end_date';

    public function handle(ProjectExpiryNotifier $notifier): int
    {
        $days = max(1, (int) $this->option('days'));

        $sent = $notifier->notifyExpiringProjects($days);

        $this->info("Sent {$sent} project expiry notification(s).");

        return self::SUCCESS;
    }
}
