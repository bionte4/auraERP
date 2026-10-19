<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList;
use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Schema;
use Livewire\Component;
use Webkul\Partner\Models\Partner;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Facades\SaleOrder as SaleManagerFacade;
use Webkul\Sale\Models\Order;

class CancelQuotationAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'orders.sales.cancel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color('gray')
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.title'))
            ->modalIcon('heroicon-s-x-circle')
            ->modalHeading(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.modal.heading'))
            ->modalDescription(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.modal.description'))
            ->action(function (Order $record, array $data, array $arguments, Component $livewire) {
                if ($arguments['cancel'] ?? false) {
                    SaleManagerFacade::cancelSaleOrder($record);

                    Notification::make()
                        ->success()
                        ->title(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.cancel.notification.cancelled.title'))
                        ->body(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.cancel.notification.cancelled.body'))
                        ->send();
                } else {

                    try {
                        SaleManagerFacade::cancelSaleOrder($record, $data ?? []);
                    } catch (Exception $e) {
                        Notification::make()
                            ->danger()
                            ->body($e->getMessage())
                            ->send();

                        $this->halt(shouldRollBackDatabaseTransaction: true);

                        return;
                    }

                    Notification::make()
                        ->success()
                        ->title(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.send-and-cancel.notification.cancelled.title'))
                        ->body(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.send-and-cancel.notification.cancelled.body'))
                        ->send();
                }

                $livewire->refreshFormData(['state']);
            })
            ->extraModalFooterActions(fn (Action $action): array => [
                $action
                    ->makeModalSubmitAction('Cancel', arguments: ['cancel' => true])
                    ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.cancel.title'))
                    ->icon('heroicon-o-x-circle')
                    ->modalIcon('heroicon-s-x-circle')
                    ->color('primary'),
            ])
            ->modalCancelAction(fn (Action $action) => $action
                ->color('gray')
                ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.close.title')))
            ->modalSubmitAction(
                fn (Action $action) => $action
                    ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.footer-actions.send-and-cancel.title'))
                    ->icon('heroicon-o-envelope')
                    ->modalIcon('heroicon-s-envelope')
            )
            ->schema(
                function (Schema $schema, $record) {
                    return $schema->components([
                                                \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::searchInput('partners_search'),
                                                CheckboxList::make('partners')
                            ->options(fn (\Filament\Schemas\Components\Utilities\Get $get) => \Webkul\Support\Filament\Forms\Components\LivewireSearchableCheckboxList::filterOptions(
                                Partner::filterExternalCustomersQuery(Partner::query())
                                    ->orderBy('partners_partners.name')
                                    ->pluck('partners_partners.name', 'partners_partners.id')
                                    ->all(),
                                $get,
                                'partners',
                            ))
                            ->default([$record->partner_id])
                            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.partner'))
                            
                            ->columns(2),
                        TextInput::make('subject')
                            ->default(fn () => __('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.subject-default', [
                                'name' => $record->name,
                                'id'   => $record->id,
                            ]))
                            ->placeholder(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.subject-placeholder'))
                            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.subject'))
                            ->hiddenLabel(),
                        RichEditor::make('description')
                            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.description'))
                            ->default(function () use ($record) {
                                return __('sales::filament/clusters/orders/resources/quotation/actions/cancel-quotation.form.fields.description-default', [
                                    'partner_name' => $record?->partner?->name,
                                    'name'         => $record?->name,
                                ]);
                            })
                            ->hiddenLabel(),
                    ]);
                }
            )
            ->hidden(fn ($record) => ! in_array($record->state, [OrderState::DRAFT, OrderState::SENT, OrderState::SALE]));
    }
}
