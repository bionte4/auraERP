<?php

namespace Webkul\TimeOff\Console\Commands;

use Illuminate\Console\Command;
use Webkul\TimeOff\Services\ApprovalEscalationService;

class EscalatePendingApprovalsCommand extends Command
{
    protected $signature = 'time-off:escalate-pending-approvals {--hours=24 : SLA threshold in hours before escalating pending leave/permit approvals}';

    protected $description = 'Escalate overdue pending leave/permit approvals up the organizational hierarchy and notify higher-level managers';

    public function handle(ApprovalEscalationService $service): int
    {
        $hours = max(1, (int) $this->option('hours'));

        $result = $service->escalateOverdueLeaves($hours);

        $this->info("Escalated {$result['escalated']} request(s) and sent {$result['notifications_sent']} notification(s).");

        return self::SUCCESS;
    }
}
