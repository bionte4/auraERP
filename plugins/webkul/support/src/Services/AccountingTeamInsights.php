<?php

namespace Webkul\Support\Services;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Enums\PaymentState;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Project\Models\Project;
use Webkul\Security\Models\Team;
use Webkul\Security\Models\User;
use Webkul\Security\Support\AdminAccess;

class AccountingTeamInsights
{
    /**
     * @return array{
     *     open_invoices: int,
     *     overdue_ar: int,
     *     overdue_ap: int,
     *     payments_pending: int,
     *     team_budget: float,
     *     scoped: bool
     * }
     */
    public function summary(?User $user = null): array
    {
        $empty = [
            'open_invoices'    => 0,
            'overdue_ar'       => 0,
            'overdue_ap'       => 0,
            'payments_pending' => 0,
            'team_budget'      => 0.0,
            'scoped'           => false,
        ];

        $user ??= auth()->user();

        if (! $user instanceof User) {
            return $empty;
        }

        try {
            $scope = $this->teamScope($user);
            $partnerIds = $scope['partner_ids'];
            $userIds = $scope['user_ids'];
            $projectIds = $scope['project_ids'];
            $scoped = $scope['scoped'];

            $budget = 0.0;

            if ($projectIds !== [] && Schema::hasTable('projects_projects')) {
                $budget = (float) Project::query()
                    ->withoutGlobalScopes()
                    ->whereIn('id', $projectIds)
                    ->sum('project_value');
            }

            if (! Schema::hasTable('accounts_account_moves')) {
                return array_merge($empty, [
                    'team_budget' => $budget,
                    'scoped'      => $scoped,
                ]);
            }

            return [
                'open_invoices'    => $this->countOpenMoves(MoveType::OUT_INVOICE, $partnerIds, $userIds, $scoped),
                'overdue_ar'       => $this->countOverdueMoves(MoveType::OUT_INVOICE, $partnerIds, $userIds, $scoped),
                'overdue_ap'       => $this->countOverdueMoves(MoveType::IN_INVOICE, $partnerIds, $userIds, $scoped),
                'payments_pending' => $this->countPendingPayments($partnerIds, $userIds, $scoped),
                'team_budget'      => $budget,
                'scoped'           => $scoped,
            ];
        } catch (Throwable) {
            return $empty;
        }
    }

    /**
     * @return array{partner_ids: array<int, int>, user_ids: array<int, int>, project_ids: array<int, int>, scoped: bool}
     */
    public function teamScope(User $user): array
    {
        // Accounting Managers always see the whole company; staff are
        // scoped to their teams below.
        if (AdminAccess::check($user)
            || (method_exists($user, 'hasRole') && $user->hasRole('Accounting Manager'))
            || ! Schema::hasTable('teams')
            || ! Schema::hasTable('user_team')) {
            return [
                'partner_ids' => [],
                'user_ids'    => [],
                'project_ids' => [],
                'scoped'      => false,
            ];
        }

        $teams = Team::query()
            ->withoutGlobalScopes()
            ->where(function ($query) use ($user): void {
                $query->where('creator_id', $user->id)
                    ->orWhereHas('users', fn ($users) => $users->where('users.id', $user->id));
            })
            ->with(['users:id'])
            ->get();

        if ($teams->isEmpty()) {
            return [
                'partner_ids' => [],
                'user_ids'    => [(int) $user->id],
                'project_ids' => [],
                'scoped'      => false,
            ];
        }

        $userIds = $teams->flatMap(fn (Team $team) => $team->users->pluck('id'))
            ->push($user->id)
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();

        $projectIds = collect();

        if (Schema::hasColumn('teams', 'project_id')) {
            $projectIds = $projectIds->merge($teams->pluck('project_id')->filter());
        }

        if (Schema::hasTable('projects_projects')) {
            $names = $teams->map(fn (Team $team): string => mb_strtolower(trim($team->name)))->unique()->values();

            $byName = Project::query()
                ->withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->where(function ($query) use ($names, $userIds): void {
                    foreach ($names as $name) {
                        $query->orWhereRaw('LOWER(TRIM(name)) = ?', [$name]);
                    }

                    $query->orWhereIn('user_id', $userIds->all());
                })
                ->pluck('id');

            $projectIds = $projectIds->merge($byName);
        }

        $projectIds = $projectIds->map(fn ($id): int => (int) $id)->unique()->values();

        $partnerIds = collect();

        if ($projectIds->isNotEmpty() && Schema::hasTable('projects_projects')) {
            $partnerIds = Project::query()
                ->withoutGlobalScopes()
                ->whereIn('id', $projectIds->all())
                ->whereNotNull('partner_id')
                ->pluck('partner_id')
                ->map(fn ($id): int => (int) $id)
                ->unique()
                ->values();
        }

        return [
            'partner_ids' => $partnerIds->all(),
            'user_ids'    => $userIds->all(),
            'project_ids' => $projectIds->all(),
            'scoped'      => true,
        ];
    }

    /**
     * @param  array<int, int>  $partnerIds
     * @param  array<int, int>  $userIds
     */
    protected function countOpenMoves(MoveType $type, array $partnerIds, array $userIds, bool $scoped): int
    {
        $query = DB::table('accounts_account_moves')
            ->where('move_type', $type->value)
            ->where('state', MoveState::POSTED->value)
            ->whereIn('payment_state', [
                PaymentState::NOT_PAID->value,
                PaymentState::PARTIAL->value,
                PaymentState::IN_PAYMENT->value,
            ]);

        if (Schema::hasColumn('accounts_account_moves', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $this->applyTeamFilters($query, $partnerIds, $userIds, $scoped);

        return (int) $query->count();
    }

    /**
     * @param  array<int, int>  $partnerIds
     * @param  array<int, int>  $userIds
     */
    protected function countOverdueMoves(MoveType $type, array $partnerIds, array $userIds, bool $scoped): int
    {
        $query = DB::table('accounts_account_moves')
            ->where('move_type', $type->value)
            ->where('state', MoveState::POSTED->value)
            ->where('amount_residual', '>', 0)
            ->whereDate('invoice_date_due', '<', now()->toDateString());

        if (Schema::hasColumn('accounts_account_moves', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        $this->applyTeamFilters($query, $partnerIds, $userIds, $scoped);

        return (int) $query->count();
    }

    /**
     * @param  array<int, int>  $partnerIds
     * @param  array<int, int>  $userIds
     */
    protected function countPendingPayments(array $partnerIds, array $userIds, bool $scoped): int
    {
        if (! Schema::hasTable('accounts_account_payments')) {
            return 0;
        }

        $query = DB::table('accounts_account_payments')
            ->whereIn('state', [
                PaymentStatus::DRAFT->value,
                PaymentStatus::IN_PROCESS->value,
                PaymentStatus::NOT_PAID->value,
            ]);

        if (Schema::hasColumn('accounts_account_payments', 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if ($scoped) {
            $query->where(function ($inner) use ($partnerIds, $userIds): void {
                if ($partnerIds !== []) {
                    $inner->whereIn('partner_id', $partnerIds);
                }

                if ($userIds !== []) {
                    $method = $partnerIds !== [] ? 'orWhereIn' : 'whereIn';
                    $inner->{$method}('creator_id', $userIds);
                }

                if ($partnerIds === [] && $userIds === []) {
                    $inner->whereRaw('1 = 0');
                }
            });
        }

        return (int) $query->count();
    }

    /**
     * @param  Builder  $query
     * @param  array<int, int>  $partnerIds
     * @param  array<int, int>  $userIds
     */
    protected function applyTeamFilters($query, array $partnerIds, array $userIds, bool $scoped): void
    {
        if (! $scoped) {
            return;
        }

        $query->where(function ($inner) use ($partnerIds, $userIds): void {
            if ($partnerIds !== []) {
                $inner->whereIn('partner_id', $partnerIds);
            }

            if ($userIds !== []) {
                $method = $partnerIds !== [] ? 'orWhereIn' : 'whereIn';
                $inner->{$method}('creator_id', $userIds);
            }

            if ($partnerIds === [] && $userIds === []) {
                $inner->whereRaw('1 = 0');
            }
        });
    }
}
