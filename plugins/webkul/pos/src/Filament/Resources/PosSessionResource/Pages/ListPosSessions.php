<?php

namespace Webkul\Pos\Filament\Resources\PosSessionResource\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Pos\Filament\Pages\PosTerminalPage;
use Webkul\Pos\Filament\Resources\PosSessionResource;
use Webkul\Pos\Models\PosRegister;
use Webkul\Pos\Services\PosService;

class ListPosSessions extends ListRecords
{
    protected static string $resource = PosSessionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('open_new_session')
                ->label('Buka Sesi Kasir Baru')
                ->icon('heroicon-o-play')
                ->color('primary')
                ->schema([
                    Select::make('pos_register_id')
                        ->label('Pilih Mesin Kasir (Register)')
                        ->options(fn (): array => PosRegister::query()->where('is_active', true)->pluck('name', 'id')->all())
                        ->default(fn () => PosRegister::query()->where('is_active', true)->value('id'))
                        ->required(),
                    TextInput::make('opening_balance')
                        ->label('Saldo Awal Kas di Laci (Rp)')
                        ->numeric()
                        ->default(0)
                        ->required(),
                    Textarea::make('opening_notes')
                        ->label('Catatan Buka Shift')
                        ->rows(2),
                ])
                ->action(function (array $data) {
                    $register = PosRegister::query()->findOrFail($data['pos_register_id']);
                    $session = app(PosService::class)->openSession(
                        $register,
                        (float) ($data['opening_balance'] ?? 0),
                        auth()->id(),
                        $data['opening_notes'] ?? null
                    );

                    Notification::make()
                        ->success()
                        ->title("Sesi {$session->name} berhasil dibuka.")
                        ->send();

                    return redirect()->to(PosTerminalPage::getUrl(['session_id' => $session->id]));
                }),
        ];
    }
}
