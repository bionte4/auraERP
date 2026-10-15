<?php

namespace Webkul\TimeOff\Services;

use Filament\Actions\Action;
use Filament\Notifications\DatabaseNotification as FilamentDatabaseNotification;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource;
use Webkul\TimeOff\Models\Leave;

class ApprovalEscalationService
{
    public const DEFAULT_SLA_HOURS = 24;

    /**
     * Find all pending Leave/Permit requests older than the SLA threshold.
     *
     * @return Collection<int, Leave>
     */
    public function overduePendingLeaves(int $slaHours = self::DEFAULT_SLA_HOURS): Collection
    {
        if (! Schema::hasTable('time_off_leaves')) {
            return collect();
        }

        $cutoff = now()->subHours(max(1, $slaHours));

        return Leave::withoutGlobalScopes()
            ->whereIn('state', [State::CONFIRM->value, State::VALIDATE_ONE->value])
            ->where('created_at', '<=', $cutoff)
            ->orderBy('created_at')
            ->get();
    }

    /**
     * Escalate overdue Leave/Permit approvals up the organizational hierarchy and notify approvers.
     *
     * @return array{escalated: int, notifications_sent: int}
     */
    public function escalateOverdueLeaves(int $slaHours = self::DEFAULT_SLA_HOURS): array
    {
        $leaves = $this->overduePendingLeaves($slaHours);

        if ($leaves->isEmpty()) {
            return ['escalated' => 0, 'notifications_sent' => 0];
        }

        $escalatedCount = 0;
        $notificationsSent = 0;

        foreach ($leaves as $leave) {
            $employee = $leave->employee()->withoutGlobalScopes()->first();

            if (! $employee) {
                continue;
            }

            $levelOneEmployee = $leave->first_approver_id
                ? Employee::withoutGlobalScopes()->find($leave->first_approver_id)
                : $employee->resolveLevelOneApproverEmployee();

            $levelTwoEmployee = $leave->second_approver_id
                ? Employee::withoutGlobalScopes()->find($leave->second_approver_id)
                : $employee->resolveLevelTwoApproverEmployee();

            $dirty = false;

            if (! $leave->manager_id && $levelOneEmployee) {
                $leave->manager_id = $levelOneEmployee->id;
                $dirty = true;
            }

            if (! $leave->first_approver_id && $levelOneEmployee) {
                $leave->first_approver_id = $levelOneEmployee->id;
                $dirty = true;
            }

            if (! $leave->second_approver_id && $levelTwoEmployee) {
                $leave->second_approver_id = $levelTwoEmployee->id;
                $dirty = true;
            }

            $currentState = $leave->state instanceof State ? $leave->state->value : (string) $leave->state;

            if ($currentState === State::CONFIRM->value && $leave->second_approver_id) {
                $leave->state = State::VALIDATE_ONE->value;
                $dirty = true;
            }

            if ($dirty) {
                $leave->saveQuietly();
                $escalatedCount++;
            }

            $waitingHours = (int) max(1, $leave->created_at?->diffInHours(now()) ?? $slaHours);
            $leaveTypeName = $leave->holidayStatus()->withoutGlobalScopes()->value('name') ?? 'Izin / Cuti';

            $title = "Eskalasi SLA Permit/Cuti: {$employee->name}";
            $body = sprintf(
                'Pengajuan %s (%s) atas nama %s telah menunggu persetujuan lebih dari %d jam dan dieskalasi ke pejabat di atasnya (%s).',
                $leaveTypeName,
                $leave->duration_display ?? ($leave->number_of_days.' hari'),
                $employee->name,
                $waitingHours,
                $levelTwoEmployee?->name ?? 'Manajemen / HRD'
            );

            $url = $this->leaveUrl($leave);

            foreach ($this->recipientsForEscalation($leave, $levelOneEmployee, $levelTwoEmployee) as $recipient) {
                if ($this->alreadyNotified($recipient, $leave->id)) {
                    continue;
                }

                $notification = Notification::make()
                    ->title($title)
                    ->body($body)
                    ->icon('heroicon-o-bolt')
                    ->color('warning');

                if ($url !== null) {
                    $notification->actions([
                        Action::make('review')
                            ->label('Tinjau & Setujui')
                            ->url($url)
                            ->markAsRead(),
                    ]);
                }

                $message = $notification->getDatabaseMessage();
                $message['leave_escalation_id'] = $leave->id;

                $recipient->notifications()->create([
                    'id'   => (string) str()->uuid(),
                    'type' => FilamentDatabaseNotification::class,
                    'data' => $message,
                ]);

                $notificationsSent++;
            }
        }

        return [
            'escalated'          => $escalatedCount,
            'notifications_sent' => $notificationsSent,
        ];
    }

    /**
     * @return Collection<int, User>
     */
    protected function recipientsForEscalation(
        Leave $leave,
        ?Employee $levelOneEmployee,
        ?Employee $levelTwoEmployee
    ): Collection {
        $userIds = [];

        if ($levelOneEmployee?->user_id) {
            $userIds[] = (int) $levelOneEmployee->user_id;
        }

        if ($levelTwoEmployee?->user_id) {
            $userIds[] = (int) $levelTwoEmployee->user_id;
        }

        if ($levelTwoEmployee?->parent_id) {
            $levelThreeUserId = Employee::withoutGlobalScopes()
                ->where('id', $levelTwoEmployee->parent_id)
                ->value('user_id');

            if ($levelThreeUserId) {
                $userIds[] = (int) $levelThreeUserId;
            }
        }

        $hrUsers = User::query()
            ->get()
            ->filter(fn (User $u): bool => $u->hasAnyRole(['Admin', 'HRD']))
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $userIds = array_values(array_unique(array_merge($userIds, $hrUsers)));

        return User::query()->whereIn('id', $userIds)->get();
    }

    protected function alreadyNotified(User $recipient, int $leaveId): bool
    {
        return $recipient->notifications()
            ->where('data->leave_escalation_id', $leaveId)
            ->exists();
    }

    protected function leaveUrl(Leave $leave): ?string
    {
        try {
            return TimeOffResource::getUrl('view', ['record' => $leave->id], panel: 'admin');
        } catch (Throwable) {
            return null;
        }
    }
}
