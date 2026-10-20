<?php

namespace Webkul\Employee\Filament\Resources\EmployeeResource\Pages;

use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Services\ContractExpiryNotifier;
use Webkul\Employee\Services\EmployeeBulkImportService;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListEmployees extends ListRecords
{
    use HasTableViews;

    protected static string $resource = EmployeeResource::class;

    public function mount(): void
    {
        parent::mount();

        $notifier = app(ContractExpiryNotifier::class);
        $notifier->notifyExpiringContracts();

        $expiringCount = $notifier->expiringEmployees()->count();

        if ($expiringCount > 0 && ! session()->has('employees.contract_expiry_toast_shown')) {
            session()->put('employees.contract_expiry_toast_shown', true);

            Notification::make()
                ->warning()
                ->icon('heroicon-o-exclamation-triangle')
                ->title(__('employees::filament/resources/employee.notifications.contract-banner.title', ['count' => $expiringCount]))
                ->body(__('employees::filament/resources/employee.notifications.contract-banner.body'))
                ->send();
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label(__('employees::filament/resources/employee/pages/list-employee.header-actions.template.label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn () => app(EmployeeBulkImportService::class)->downloadTemplate()),

            Action::make('bulkImport')
                ->label(__('employees::filament/resources/employee/pages/list-employee.header-actions.import.label'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading(__('employees::filament/resources/employee/pages/list-employee.header-actions.import.modal-heading'))
                ->modalDescription(__('employees::filament/resources/employee/pages/list-employee.header-actions.import.modal-description'))
                ->schema([
                    FileUpload::make('file')
                        ->label(__('employees::filament/resources/employee/pages/list-employee.header-actions.import.file-label'))
                        ->disk('local')
                        ->directory('imports/employees')
                        ->acceptedFileTypes([
                            'text/csv',
                            'text/plain',
                            'application/vnd.ms-excel',
                            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        ])
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $relativePath = (string) ($data['file'] ?? '');
                    $fullPath = Storage::disk('local')->path($relativePath);

                    $importService = app(EmployeeBulkImportService::class);
                    $result = $importService->importFromPath($fullPath);

                    Storage::disk('local')->delete($relativePath);

                    Notification::make()
                        ->success()
                        ->title(__('employees::filament/resources/employee/pages/list-employee.header-actions.import.notification.title'))
                        ->body(__('employees::filament/resources/employee/pages/list-employee.header-actions.import.notification.body', $result))
                        ->send();

                    $unknownTypes = $importService->unknownEmploymentTypes();

                    if ($unknownTypes !== []) {
                        Notification::make()
                            ->warning()
                            ->title(__('employees::filament/resources/employee/pages/list-employee.header-actions.import.unknown-types.title'))
                            ->body(__('employees::filament/resources/employee/pages/list-employee.header-actions.import.unknown-types.body', ['types' => implode(', ', $unknownTypes)]))
                            ->persistent()
                            ->send();
                    }
                }),

            CreateAction::make()
                ->icon('heroicon-o-plus-circle')
                ->label(__('employees::filament/resources/employee/pages/list-employee.header-actions.create.label')),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_team' => PresetView::make(__('employees::filament/resources/employee/pages/list-employee.tabs.my-team'))
                ->icon('heroicon-m-users')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    $user = Auth::user();

                    if (! $user->employee) {
                        return $query->whereNull('id');
                    }

                    return $query->where('parent_id', $user->employee->id);
                }),

            'my_department' => PresetView::make(__('employees::filament/resources/employee/pages/list-employee.tabs.my-department'))
                ->icon('heroicon-m-user-group')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    $user = Auth::user();

                    if (! $user->employee) {
                        return $query->whereNull('id');
                    }

                    return $query->where('department_id', $user->employee->department_id);
                }),

            'archived' => PresetView::make(__('employees::filament/resources/employee/pages/list-employee.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
            'newly_hired' => PresetView::make(__('employees::filament/resources/employee/pages/list-employee.tabs.newly-hired'))
                ->icon('heroicon-s-calendar')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query->where(function (Builder $subQuery) {
                        $subQuery->where('join_date', '>=', Carbon::now()->subMonth()->toDateString())
                            ->orWhere('created_at', '>=', Carbon::now()->subMonth());
                    });
                }),

            'expiring_contracts' => PresetView::make(__('employees::filament/resources/employee/pages/list-employee.tabs.expiring-contracts'))
                ->icon('heroicon-s-exclamation-triangle')
                ->favorite()
                ->modifyQueryUsing(function (Builder $query) {
                    return $query
                        ->where('is_active', true)
                        ->whereNotNull('contract_end_date')
                        ->whereDate('contract_end_date', '<=', Carbon::now()->addDays(30)->toDateString());
                }),
        ];
    }
}
