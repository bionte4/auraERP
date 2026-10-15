<?php

namespace Webkul\Pos\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;
use Webkul\Pos\Filament\Pages\PosTerminalPage;
use Webkul\Pos\Filament\Resources\PosOrderResource\Pages\ListPosOrders;
use Webkul\Pos\Models\PosOrder;
use Webkul\Pos\Services\PosService;
use Webkul\Support\Enums\NavigationGroup;

class PosOrderResource extends Resource
{
    protected static ?string $model = PosOrder::class;

    protected static ?string $slug = 'pos/orders';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return NavigationGroup::Pos;
    }

    public static function getNavigationLabel(): string
    {
        return 'Riwayat Transaksi & Struk';
    }

    public static function getModelLabel(): string
    {
        return 'Transaksi POS';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Riwayat Transaksi & Struk POS';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('No. Struk')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('session.name')
                    ->label('Sesi / Shift')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('register.name')
                    ->label('Mesin Kasir')
                    ->sortable(),
                TextColumn::make('partner.name')
                    ->label('Pelanggan')
                    ->placeholder('Pelanggan Umum')
                    ->searchable(),
                TextColumn::make('cashier.name')
                    ->label('Kasir')
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        PosOrder::STATUS_PAID     => 'success',
                        PosOrder::STATUS_PARKED   => 'warning',
                        PosOrder::STATUS_REFUNDED => 'danger',
                        default                   => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        PosOrder::STATUS_PAID     => 'Lunas (Paid)',
                        PosOrder::STATUS_PARKED   => 'Ditahan (Hold)',
                        PosOrder::STATUS_REFUNDED => 'Diretur (Refund)',
                        default                   => $state,
                    }),
                TextColumn::make('total_amount')
                    ->label('Total Belanja')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('paid_amount')
                    ->label('Dibayar')
                    ->money('IDR', locale: 'id'),
                TextColumn::make('change_amount')
                    ->label('Kembalian')
                    ->money('IDR', locale: 'id'),
                IconColumn::make('stock_deducted')
                    ->label('Stok Dipotong')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Waktu Transaksi')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Transaksi')
                    ->options([
                        PosOrder::STATUS_PAID     => 'Lunas (Paid)',
                        PosOrder::STATUS_PARKED   => 'Ditahan (Hold)',
                        PosOrder::STATUS_REFUNDED => 'Diretur (Refund)',
                    ]),
            ])
            ->recordActions([
                Action::make('print_receipt')
                    ->label('Struk')
                    ->icon('heroicon-o-printer')
                    ->color('primary')
                    ->url(fn (PosOrder $record): string => PosTerminalPage::getUrl([
                        'session_id'       => $record->pos_session_id,
                        'register_id'      => $record->pos_register_id,
                        'receipt_order_id' => $record->id,
                    ])),
                Action::make('whatsapp_receipt')
                    ->label('WhatsApp')
                    ->icon('heroicon-o-chat-bubble-left-ellipsis')
                    ->color('success')
                    ->url(fn (PosOrder $record): string => app(PosService::class)->buildWhatsappReceiptUrl($record), shouldOpenInNewTab: true),
                Action::make('refund_order')
                    ->label('Retur / Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('danger')
                    ->visible(fn (PosOrder $record): bool => $record->status === PosOrder::STATUS_PAID)
                    ->requiresConfirmation()
                    ->schema([
                        Textarea::make('reason')
                            ->label('Alasan Retur / Refund (Stok akan dikembalikan otomatis ke gudang)')
                            ->required()
                            ->default('Retur barang pelanggan'),
                    ])
                    ->action(function (PosOrder $record, array $data): void {
                        app(PosService::class)->refundOrder($record, $data['reason'] ?? null);

                        Notification::make()
                            ->success()
                            ->title("Transaksi {$record->name} berhasil diretur & stok gudang telah dikembalikan.")
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosOrders::route('/'),
        ];
    }
}
