<?php

namespace Webkul\Project\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Project\Services\TeamMembershipSynchronizer;

class SyncTeamMembershipsCommand extends Command
{
    protected $signature = 'projects:sync-team-memberships';

    protected $description = 'Sync Team members from Project Managers and assigned project employees (match by project/team name)';

    public function handle(TeamMembershipSynchronizer $synchronizer): int
    {
        $result = $synchronizer->syncAll();

        $this->info(sprintf(
            'Synced %d project(s) into %d team(s); attached %d new membership(s).',
            $result['projects'],
            $result['teams'],
            $result['attached'],
        ));

        return self::SUCCESS;
    }
}
