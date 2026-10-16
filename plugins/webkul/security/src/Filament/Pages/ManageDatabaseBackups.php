<?php

namespace Webkul\Security\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Throwable;
use Webkul\Security\Models\User;
use Webkul\Security\Services\DatabaseBackupManager;
use Webkul\Support\Enums\NavigationGroup;

class ManageDatabaseBackups extends Page
{
    protected string $view = 'security::filament.pages.manage-database-backups';

    protected static ?string $slug = 'database-backups';

    protected static ?int $navigationSort = 9;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $backups = [];

    public ?string $pendingRestoreFilename = null;

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);
        $this->refreshBackups();
    }

    public static function canAccess(): bool
    {
        return static::viewerIsAdmin();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::viewerIsAdmin();
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/pages/manage-database-backups.navigation.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('security::filament/pages/manage-database-backups.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function refreshBackups(): void
    {
        $this->backups = app(DatabaseBackupManager::class)->list()->all();
    }

    protected function getHeaderActions(): array
    {
        $phrase = (string) config('security-backup.restore_confirmation', 'RESTORE');

        return [
            Action::make('createBackup')
                ->label(__('security::filament/pages/manage-database-backups.actions.create'))
                ->icon('heroicon-o-plus-circle')
                ->requiresConfirmation()
                ->modalHeading(__('security::filament/pages/manage-database-backups.actions.create-heading'))
                ->modalDescription(__('security::filament/pages/manage-database-backups.actions.create-description'))
                ->action(function (): void {
                    try {
                        $meta = app(DatabaseBackupManager::class)->create('manual');

                        Notification::make()
                            ->success()
                            ->title(__('security::filament/pages/manage-database-backups.notifications.created.title'))
                            ->body(__('security::filament/pages/manage-database-backups.notifications.created.body', [
                                'file' => $meta['filename'],
                            ]))
                            ->send();
                    } catch (Throwable $exception) {
                        Notification::make()
                            ->danger()
                            ->title(__('security::filament/pages/manage-database-backups.notifications.failed.title'))
                            ->body($exception->getMessage())
                            ->send();
                    }

                    $this->refreshBackups();
                }),
            Action::make('pruneBackups')
                ->label(__('security::filament/pages/manage-database-backups.actions.prune'))
                ->icon('heroicon-o-trash')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function (): void {
                    $deleted = app(DatabaseBackupManager::class)->prune();

                    Notification::make()
                        ->success()
                        ->title(__('security::filament/pages/manage-database-backups.notifications.pruned.title'))
                        ->body(__('security::filament/pages/manage-database-backups.notifications.pruned.body', [
                            'count' => $deleted,
                        ]))
                        ->send();

                    $this->refreshBackups();
                }),
            Action::make('confirmRestore')
                ->label(__('security::filament/pages/manage-database-backups.actions.restore'))
                ->icon('heroicon-o-arrow-path')
                ->color('danger')
                ->modalHeading(__('security::filament/pages/manage-database-backups.actions.restore-heading'))
                ->modalDescription(__('security::filament/pages/manage-database-backups.actions.restore-description', [
                    'phrase' => $phrase,
                ]))
                ->form([
                    TextInput::make('confirmation')
                        ->label(__('security::filament/pages/manage-database-backups.actions.restore-confirm-label', [
                            'phrase' => $phrase,
                        ]))
                        ->required()
                        ->rule('in:'.$phrase),
                ])
                ->action(function (): void {
                    abort_unless(static::canAccess(), 403);

                    $filename = (string) $this->pendingRestoreFilename;

                    if ($filename === '') {
                        return;
                    }

                    try {
                        app(DatabaseBackupManager::class)->restore($filename);

                        Notification::make()
                            ->success()
                            ->title(__('security::filament/pages/manage-database-backups.notifications.restored.title'))
                            ->body(__('security::filament/pages/manage-database-backups.notifications.restored.body', [
                                'file' => $filename,
                            ]))
                            ->send();
                    } catch (Throwable $exception) {
                        Notification::make()
                            ->danger()
                            ->title(__('security::filament/pages/manage-database-backups.notifications.failed.title'))
                            ->body($exception->getMessage())
                            ->send();
                    }

                    $this->pendingRestoreFilename = null;
                    $this->refreshBackups();
                })
                ->extraAttributes(['class' => 'hidden']),
        ];
    }

    public function deleteBackup(string $filename): void
    {
        abort_unless(static::canAccess(), 403);

        try {
            app(DatabaseBackupManager::class)->delete($filename);

            Notification::make()
                ->success()
                ->title(__('security::filament/pages/manage-database-backups.notifications.deleted.title'))
                ->send();
        } catch (Throwable $exception) {
            Notification::make()
                ->danger()
                ->title(__('security::filament/pages/manage-database-backups.notifications.failed.title'))
                ->body($exception->getMessage())
                ->send();
        }

        $this->refreshBackups();
    }

    public function requestRestore(string $filename): void
    {
        abort_unless(static::canAccess(), 403);

        $this->pendingRestoreFilename = basename($filename);
        $this->mountAction('confirmRestore');
    }

    /**
     * @return array<string, mixed>
     */
    public function getViewData(): array
    {
        return [
            'scheduleAt'     => (string) config('security-backup.schedule_at', '02:00'),
            'retentionDays'  => (int) config('security-backup.retention_days', 14),
            'retentionCount' => (int) config('security-backup.retention_count', 30),
            'enabled'        => (bool) config('security-backup.enabled', true),
            'directory'      => app(DatabaseBackupManager::class)->directory(),
        ];
    }

    protected static function viewerIsAdmin(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User || ! method_exists($user, 'hasRole')) {
            return false;
        }

        return $user->hasRole(config('filament-shield.panel_user.name', 'Admin'));
    }
}
