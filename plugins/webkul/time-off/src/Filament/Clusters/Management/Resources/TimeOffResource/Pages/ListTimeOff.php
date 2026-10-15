<?php

namespace Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Employee\Models\Employee;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;
use Webkul\TimeOff\Enums\State;
use Webkul\TimeOff\Filament\Clusters\Management\Resources\TimeOffResource;
use Webkul\TimeOff\Services\ApprovalEscalationService;

class ListTimeOff extends ListRecords
{
    use HasTableViews;

    protected static string $resource = TimeOffResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'waiting_for_me' => PresetView::make(__('time-off::filament/clusters/management/resources/time-off/pages/list-time-off.preset-views.waiting-for-me'))
                ->icon('heroicon-o-user-circle')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [
                    State::CONFIRM->value,
                    State::VALIDATE_ONE->value,
                ])),
            'second_approval' => PresetView::make(__('time-off::filament/clusters/management/resources/time-off/pages/list-time-off.preset-views.second-approval'))
                ->icon('heroicon-o-shield-check')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('state', [
                    State::CONFIRM->value,
                    State::VALIDATE_TWO->value,
                ])),
            'approved' => PresetView::make(__('time-off::filament/clusters/management/resources/time-off/pages/list-time-off.preset-views.approved'))
                ->icon('heroicon-o-check-badge')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', State::VALIDATE_TWO->value)),
            'valid' => PresetView::make(__('time-off::filament/clusters/management/resources/time-off/pages/list-time-off.preset-views.currently-valid'))
                ->icon('heroicon-o-check')
                ->modifyQueryUsing(function (Builder $query) {
                    $today = now()->format('Y-m-d');

                    return $query
                        ->where(function ($query) use ($today) {
                            $query
                                ->whereDate('date_from', '<=', $today)
                                ->whereDate('date_to', '>=', $today);
                        });
                }),
            'my_team' => PresetView::make(__('time-off::filament/clusters/management/resources/time-off/pages/list-time-off.preset-views.my-team'))
                ->icon('heroicon-o-users')
                ->modifyQueryUsing(function (Builder $query) {
                    $currentUserId = Auth::id();
                    $managerEmployee = Employee::withoutGlobalScopes()->where('user_id', $currentUserId)->first();
                    $subordinateIds = $managerEmployee ? $managerEmployee->getSubordinateEmployeeIds(true) : [];

                    return $query->where(function (Builder $outer) use ($currentUserId, $managerEmployee, $subordinateIds) {
                        if (! empty($subordinateIds)) {
                            $outer->orWhereIn('employee_id', $subordinateIds);
                        }

                        if ($managerEmployee) {
                            $outer->orWhere('first_approver_id', $managerEmployee->id)
                                ->orWhere('second_approver_id', $managerEmployee->id)
                                ->orWhere('manager_id', $managerEmployee->id);
                        }

                        $outer->orWhereHas('employee', function ($q) use ($currentUserId) {
                            $q->where('leave_manager_id', '=', $currentUserId)
                                ->orWhere('user_id', '=', $currentUserId);
                        });
                    });
                }),
            'my_department' => PresetView::make(__('time-off::filament/clusters/management/resources/time-off/pages/list-time-off.preset-views.my-department'))
                ->icon('heroicon-o-building-office')
                ->modifyQueryUsing(function (Builder $query) {
                    $currentUserId = Auth::id();
                    $managerEmployee = Employee::withoutGlobalScopes()->where('user_id', $currentUserId)->first();
                    $subordinateIds = $managerEmployee ? $managerEmployee->getSubordinateEmployeeIds(true) : [];

                    return $query->where(function (Builder $outer) use ($currentUserId, $subordinateIds) {
                        if (! empty($subordinateIds)) {
                            $outer->orWhereIn('employee_id', $subordinateIds);
                        }

                        $outer->orWhereHas('employee', function ($q) use ($currentUserId) {
                            $q->whereHas('parent', function ($p) use ($currentUserId) {
                                $p->where('user_id', '=', $currentUserId);
                            });
                        });
                    });
                }),
            'refused' => PresetView::make(__('time-off::filament/clusters/management/resources/time-off/pages/list-time-off.preset-views.refused'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('state', State::REFUSE->value)),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('escalate_sla')
                ->label('Eskalasi SLA Permit (24j)')
                ->icon('heroicon-o-bolt')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Jalankan Eskalasi SLA Permit / Cuti')
                ->modalDescription('Semua pengajuan izin/cuti yang belum disetujui lebih dari 24 jam akan otomatis dieskalasikan ke pejabat level di atasnya sesuai struktur organisasi.')
                ->action(function (): void {
                    $result = app(ApprovalEscalationService::class)->escalateOverdueLeaves(24);

                    Notification::make()
                        ->success()
                        ->title('Eskalasi SLA Selesai')
                        ->body("Berhasil mengeskalasi {$result['escalated']} pengajuan dan mengirim {$result['notifications_sent']} notifikasi ke atasan.")
                        ->send();
                }),
            CreateAction::make(),
        ];
    }
}
