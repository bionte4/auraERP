<?php

namespace Webkul\Partner\Filament\Resources\PartnerResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Filament\Resources\PartnerResource;
use Webkul\Partner\Services\PartnerBulkImportService;
use Webkul\PluginManager\Package;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListPartners extends ListRecords
{
    use HasTableViews;

    protected static string $resource = PartnerResource::class;

    public function getTitle(): string|Htmlable
    {
        return __('partners::filament/resources/partner/pages/list-partners.title');
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label(__('partners::filament/resources/partner/pages/list-partners.header-actions.template.label'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn () => app(PartnerBulkImportService::class)->downloadTemplate()),

            Action::make('bulkImport')
                ->label(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.label'))
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalHeading(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.modal-heading'))
                ->modalDescription(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.modal-description'))
                ->schema([
                    FileUpload::make('file')
                        ->label(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.file-label'))
                        ->disk('local')
                        ->directory('imports/partners')
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

                    $result = app(PartnerBulkImportService::class)->importFromPath($fullPath);

                    Storage::disk('local')->delete($relativePath);

                    Notification::make()
                        ->success()
                        ->title(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.notification.title'))
                        ->body(__('partners::filament/resources/partner/pages/list-partners.header-actions.import.notification.body', $result))
                        ->send();
                }),

            CreateAction::make()
                ->label(__('partners::filament/resources/partner/pages/list-partners.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }

    public function getPresetTableViews(): array
    {
        $views = [
            'individuals' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.individuals'))
                ->icon('heroicon-s-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_type', AccountType::INDIVIDUAL)),

            'companies' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.companies'))
                ->icon('heroicon-s-building-office')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('account_type', AccountType::COMPANY)),

            'employees' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.employees'))
                ->icon('heroicon-s-identification')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('sub_type', 'employee')),

            'customers' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.customers'))
                ->icon('heroicon-s-shopping-bag')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('customer_rank', '>', 0)),

            'vendors' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.vendors'))
                ->icon('heroicon-s-truck')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('supplier_rank', '>', 0)),

            'archived' => PresetView::make(__('partners::filament/resources/partner/pages/list-partners.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];

        // Customer and vendor ranks are columns the accounts plugin adds.
        if (! Package::isPluginInstalled('accounts')) {
            unset($views['customers'], $views['vendors']);
        }

        return $views;
    }
}
