<?php

namespace Webkul\Security\Filament\Resources\RoleResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Security\Filament\Resources\RoleResource;
use Webkul\Security\Services\RolePermissionSynchronizer;

class ListRoles extends ListRecords
{
    protected static string $resource = RoleResource::class;

    protected function getActions(): array
    {
        return [
            Action::make('sync_rbac_permissions')
                ->label('Sinkronkan Hak Akses & Role Standar')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function (): void {
                    $result = app(RolePermissionSynchronizer::class)->syncAll(true);

                    Notification::make()
                        ->success()
                        ->title('Sinkronisasi RBAC Berhasil')
                        ->body(sprintf(
                            '%d hak akses berhasil disinkronkan ke Role: %s',
                            $result['permissions_count'],
                            implode(', ', $result['roles_synced'])
                        ))
                        ->send();
                }),
            CreateAction::make()->icon('heroicon-o-plus-circle'),
        ];
    }
}
