<?php

namespace Webkul\Maintenance\Filament\Resources\EquipmentResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webkul\Employee\Models\Employee;
use Webkul\Maintenance\Filament\Resources\EquipmentResource;
use Webkul\Maintenance\Services\EquipmentBulkImportService;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListEquipment extends ListRecords
{
    use HasTableViews;

    protected static string $resource = EquipmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.template.label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn () => app(EquipmentBulkImportService::class)->downloadTemplate()),

            Action::make('bulkImport')
                ->label(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.import.label'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.import.modal-heading'))
                ->modalDescription(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.import.modal-description'))
                ->schema([
                    FileUpload::make('file')
                        ->label(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.import.file-label'))
                        ->disk('local')
                        ->directory('imports/equipments')
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

                    $result = app(EquipmentBulkImportService::class)->importFromPath($fullPath);

                    Storage::disk('local')->delete($relativePath);

                    Notification::make()
                        ->success()
                        ->title(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.import.notification.title'))
                        ->body(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.import.notification.body', $result))
                        ->send();
                }),

            CreateAction::make()
                ->label(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.create.notification.title'))
                        ->body(__('maintenance::filament/resources/equipment/pages/list-equipment.header-actions.create.notification.body')),
                ),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'my_equipment' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.my-equipment'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(function (Builder $query): Builder {
                    $userId = Auth::id();
                    $employeeId = $userId
                        ? Employee::withoutGlobalScopes()->where('user_id', $userId)->value('id')
                        : null;

                    return $query->where(function (Builder $sub) use ($userId, $employeeId): void {
                        $sub->where('owner_user_id', $userId);

                        if ($employeeId) {
                            $sub->orWhere('employee_id', $employeeId);
                        }
                    });
                }),

            'employee_assets' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.employee-assets'))
                ->icon('heroicon-s-identification')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('employee_id')),

            'project_customer_assets' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.project-customer-assets'))
                ->icon('heroicon-s-briefcase')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where(fn (Builder $sub) => $sub->whereNotNull('project_id')->orWhereNotNull('customer_id'))),

            'assigned' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.assigned'))
                ->icon('heroicon-s-check-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('owner_user_id')),

            'unassigned' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.unassigned'))
                ->icon('heroicon-s-minus-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('owner_user_id')),

            'under_maintenance' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.under-maintenance'))
                ->icon('heroicon-s-wrench-screwdriver')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('maintenance_open_count', '>', 0)),

            'archived' => PresetView::make(__('maintenance::filament/resources/equipment/pages/list-equipment.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
