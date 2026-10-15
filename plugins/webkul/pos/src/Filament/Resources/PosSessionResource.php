<?php

namespace Webkul\Pos\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;
use Webkul\Pos\Filament\Pages\PosTerminalPage;
use Webkul\Pos\Filament\Resources\PosSessionResource\Pages\ListPosSessions;
use Webkul\Pos\Models\PosSession;
use Webkul\Pos\Services\PosService;
use Webkul\Support\Enums\NavigationGroup;

class PosSessionResource extends Resource
{
    protected static ?string $model = PosSession::class;

    protected static ?string $slug = 'pos/sessions';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return NavigationGroup::Pos;
    }

    public static function getNavigationLabel(): string
    {
        return 'Sesi Kasir / Shift (Z-Report)';
    }

    public static function getModelLabel(): string
    {
        return 'Sesi Kasir';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Sesi Kasir / Shift (Z-Report)';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('No. Sesi')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('register.name')
                    ->label('Mesin Kasir')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('Kasir')
                    ->searchable()
                    ->placeholder('-'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => $state === PosSession::STATUS_OPEN ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state): string => $state === PosSession::STATUS_OPEN ? 'Terbuka (Aktif)' : 'Ditutup'),
                TextColumn::make('opening_balance')
                    ->label('Saldo Awal')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('cash_sales_total')
                    ->label('Penjualan Tunai')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('non_cash_sales_total')
                    ->label('Non-Tunai (QRIS/Bank/EDC)')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('expected_closing_cash')
                    ->label('Kas Seharusnya')
                    ->money('IDR', locale: 'id')
                    ->sortable(),
                TextColumn::make('closing_cash_counted')
                    ->label('Kas Aktual')
                    ->money('IDR', locale: 'id')
                    ->placeholder('-'),
                TextColumn::make('cash_difference')
                    ->label('Selisih Kas')
                    ->money('IDR', locale: 'id')
                    ->color(fn ($state): string => (float) $state < 0 ? 'danger' : ((float) $state > 0 ? 'warning' : 'success'))
                    ->placeholder('-'),
                TextColumn::make('opened_at')
                    ->label('Waktu Buka')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('closed_at')
                    ->label('Waktu Tutup')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('-'),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status Sesi')
                    ->options([
                        PosSession::STATUS_OPEN   => 'Terbuka (Aktif)',
                        PosSession::STATUS_CLOSED => 'Ditutup',
                    ]),
            ])
            ->recordActions([
                Action::make('enter_terminal')
                    ->label('Masuk Kasir')
                    ->icon('heroicon-o-calculator')
                    ->color('primary')
                    ->visible(fn (PosSession $record): bool => $record->status === PosSession::STATUS_OPEN)
                    ->url(fn (PosSession $record): string => PosTerminalPage::getUrl(['session_id' => $record->id])),
                Action::make('close_session')
                    ->label('Tutup Shift')
                    ->icon('heroicon-o-lock-closed')
                    ->color('danger')
                    ->visible(fn (PosSession $record): bool => $record->status === PosSession::STATUS_OPEN)
                    ->fillForm(fn (PosSession $record): array => [
                        'closing_cash_counted' => (float) $record->expected_closing_cash,
                        'closing_notes'        => '',
                    ])
                    ->schema([
                        TextInput::make('closing_cash_counted')
                            ->label('Uang Fisik Aktual Dihitung di Laci (Rp)')
                            ->numeric()
                            ->required(),
                        Textarea::make('closing_notes')
                            ->label('Catatan Penutupan Shift (Z-Report)')
                            ->rows(2),
                    ])
                    ->action(function (PosSession $record, array $data): void {
                        app(PosService::class)->closeSession(
                            $record,
                            (float) ($data['closing_cash_counted'] ?? 0),
                            $data['closing_notes'] ?? null
                        );

                        Notification::make()
                            ->success()
                            ->title("Sesi {$record->name} berhasil ditutup.")
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPosSessions::route('/'),
        ];
    }
}
