<?php

namespace Webkul\Attendance\Filament\Resources\AttendanceResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Attendance\Enums\ClockResult;
use Webkul\Attendance\Filament\Resources\AttendanceResource;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceClock;
use Webkul\Employee\Models\Employee;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ManageAttendances extends ManageRecords
{
    use HasTableViews;

    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            $this->clockInAction(),
            $this->clockOutAction(),
            CreateAction::make()
                ->label(__('attendances::filament/resources/attendance/manage-attendances.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'today' => PresetView::make(__('attendances::filament/resources/attendance/manage-attendances.tabs.today'))
                ->icon('heroicon-o-calendar')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereDate('work_date', now()->toDateString())),
            'mine' => PresetView::make(__('attendances::filament/resources/attendance/manage-attendances.tabs.mine'))
                ->icon('heroicon-o-user')
                ->modifyQueryUsing(function (Builder $query): Builder {
                    $employeeId = Employee::query()->where('user_id', Auth::id())->value('id');

                    return $query->where('employee_id', $employeeId ?? 0);
                }),
        ];
    }

    protected function clockInAction(): Action
    {
        return Action::make('clockIn')
            ->label(__('attendances::filament/resources/attendance/manage-attendances.header-actions.clock-in.label'))
            ->icon('heroicon-o-arrow-right-end-on-rectangle')
            ->color('success')
            ->visible(fn (): bool => Auth::user()?->can('create', Attendance::class) ?? false)
            ->action(function (AttendanceClock $clock): void {
                $employee = $clock->employeeForUser((int) Auth::id());

                if (! $employee) {
                    $this->notifyClock('danger', 'missing-employee');

                    return;
                }

                $result = $clock->clockIn($employee);

                $this->notifyClock(
                    $result === ClockResult::Recorded ? 'success' : 'warning',
                    $result === ClockResult::Recorded ? 'clock-in' : 'already-in',
                );
            });
    }

    protected function clockOutAction(): Action
    {
        return Action::make('clockOut')
            ->label(__('attendances::filament/resources/attendance/manage-attendances.header-actions.clock-out.label'))
            ->icon('heroicon-o-arrow-left-start-on-rectangle')
            ->color('gray')
            ->visible(fn (): bool => Auth::user()?->can('update_attendance_attendance') ?? false)
            ->action(function (AttendanceClock $clock): void {
                $employee = $clock->employeeForUser((int) Auth::id());

                if (! $employee) {
                    $this->notifyClock('danger', 'missing-employee');

                    return;
                }

                $result = $clock->clockOut($employee);

                $this->notifyClock(
                    $result === ClockResult::Recorded ? 'success' : 'warning',
                    $result === ClockResult::Recorded ? 'clock-out' : 'missing-open',
                );
            });
    }

    protected function notifyClock(string $status, string $key): void
    {
        $notification = Notification::make()
            ->title(__('attendances::filament/resources/attendance/manage-attendances.notifications.'.$key.'.title'))
            ->body(__('attendances::filament/resources/attendance/manage-attendances.notifications.'.$key.'.body'));

        match ($status) {
            'success' => $notification->success(),
            'warning' => $notification->warning(),
            default   => $notification->danger(),
        };

        $notification->send();
    }
}
