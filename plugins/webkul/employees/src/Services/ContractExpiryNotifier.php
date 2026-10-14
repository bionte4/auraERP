<?php

namespace Webkul\Employee\Services;

use Filament\Actions\Action;
use Filament\Notifications\DatabaseNotification as FilamentDatabaseNotification;
use Filament\Notifications\Notification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\User;

class ContractExpiryNotifier
{
    public const ALERT_WINDOW_DAYS = 30;

    /**
     * @return Collection<int, Employee>
     */
    public function expiringEmployees(int $withinDays = self::ALERT_WINDOW_DAYS): Collection
    {
        if (! Schema::hasColumn('employees_employees', 'contract_end_date')) {
            return collect();
        }

        return Employee::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where('is_active', true)
            ->whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', '<=', now()->addDays($withinDays)->toDateString())
            ->orderBy('contract_end_date')
            ->get();
    }

    public function notifyExpiringContracts(int $withinDays = self::ALERT_WINDOW_DAYS): int
    {
        $employees = $this->expiringEmployees($withinDays);

        if ($employees->isEmpty()) {
            return 0;
        }

        $sentCount = 0;

        foreach ($employees as $employee) {
            $days = $employee->daysUntilContractEnds();

            if ($days === null || $days > $withinDays) {
                continue;
            }

            $endDateString = $employee->contract_end_date->toDateString();
            $formattedDate = $employee->contract_end_date->format('d M Y');

            $title = $days < 0
                ? __('employees::filament/resources/employee.notifications.contract-expired.title', ['name' => $employee->name])
                : __('employees::filament/resources/employee.notifications.contract-expiring.title', ['name' => $employee->name]);

            $body = $days < 0
                ? __('employees::filament/resources/employee.notifications.contract-expired.body', [
                    'name' => $employee->name,
                    'date' => $formattedDate,
                ])
                : __('employees::filament/resources/employee.notifications.contract-expiring.body', [
                    'name' => $employee->name,
                    'days' => $days,
                    'date' => $formattedDate,
                ]);

            $url = $this->employeeViewUrl($employee);

            foreach ($this->recipientsFor($employee) as $recipient) {
                if ($this->alreadyNotified($recipient, $employee->id, $endDateString)) {
                    continue;
                }

                $notification = Notification::make()
                    ->title($title)
                    ->body($body)
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color($days < 0 ? 'danger' : 'warning');

                if ($url !== null) {
                    $notification->actions([
                        Action::make('view')
                            ->label(__('employees::filament/resources/employee.notifications.view-employee'))
                            ->url($url)
                            ->markAsRead(),
                    ]);
                }

                $message = $notification->getDatabaseMessage();
                $message['contract_expiry_employee_id'] = $employee->id;
                $message['contract_expiry_end_date'] = $endDateString;

                $recipient->notifications()->create([
                    'id'   => (string) str()->uuid(),
                    'type' => FilamentDatabaseNotification::class,
                    'data' => $message,
                ]);

                $sentCount++;
            }
        }

        return $sentCount;
    }

    /**
     * @return Collection<int, User>
     */
    protected function recipientsFor(Employee $employee): Collection
    {
        $users = User::query()
            ->get()
            ->filter(fn (User $user): bool => $user->can('view_any_employee_employee') || $user->can('update_employee_employee'));

        if ($employee->user_id) {
            $employeeUser = User::query()->find($employee->user_id);

            if ($employeeUser) {
                $users->push($employeeUser);
            }
        }

        if ($employee->parent?->user_id) {
            $managerUser = User::query()->find($employee->parent->user_id);

            if ($managerUser) {
                $users->push($managerUser);
            }
        }

        return $users->unique('id')->values();
    }

    protected function alreadyNotified(User $user, int $employeeId, string $endDate): bool
    {
        return $user->notifications()
            ->where('type', FilamentDatabaseNotification::class)
            ->get()
            ->contains(function ($notification) use ($employeeId, $endDate): bool {
                $data = (array) $notification->data;

                return ($data['contract_expiry_employee_id'] ?? null) === $employeeId
                    && ($data['contract_expiry_end_date'] ?? null) === $endDate;
            });
    }

    protected function employeeViewUrl(Employee $employee): ?string
    {
        try {
            return EmployeeResource::getUrl('view', ['record' => $employee]);
        } catch (\Throwable) {
            return url('/admin/employees/employees/'.$employee->id);
        }
    }
}
