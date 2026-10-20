<?php

namespace Webkul\Security\Filament\Pages;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\Width;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Security\Models\BlockedIp;
use Webkul\Security\Services\LoginProtection;
use Webkul\Security\Support\AdminAccess;
use Webkul\Support\Enums\NavigationGroup;

class ManageBlockedIps extends Page
{
    protected string $view = 'security::filament.pages.manage-blocked-ips';

    protected static ?string $slug = 'blocked-ips';

    protected static ?int $navigationSort = 10;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-no-symbol';

    /**
     * @var array<int, array<string, mixed>>
     */
    public array $blockedIps = [];

    public function mount(): void
    {
        AdminAccess::ensure();
        $this->refreshBlockedIps();
    }

    public static function canAccess(): bool
    {
        return AdminAccess::check();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return AdminAccess::check();
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/pages/manage-blocked-ips.navigation.label');
    }

    public function getTitle(): string|Htmlable
    {
        return __('security::filament/pages/manage-blocked-ips.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public function getMaxContentWidth(): Width|string|null
    {
        return Width::Full;
    }

    public function refreshBlockedIps(): void
    {
        $this->blockedIps = BlockedIp::query()
            ->whereNotNull('blocked_until')
            ->where('blocked_until', '>', now())
            ->orderByDesc('blocked_until')
            ->get()
            ->map(fn (BlockedIp $record): array => [
                'id'              => $record->id,
                'ip_address'      => $record->ip_address,
                'failed_attempts' => $record->failed_attempts,
                'reason'          => $record->reason,
                'last_email'      => $record->last_email,
                'blocked_until'   => $record->blocked_until?->timezone(config('app.timezone'))->format('Y-m-d H:i:s'),
            ])
            ->all();
    }

    public function unblockIp(string $ip): void
    {
        AdminAccess::ensure();

        app(LoginProtection::class)->unblock($ip);
        $this->refreshBlockedIps();

        Notification::make()
            ->success()
            ->title(__('security::filament/pages/manage-blocked-ips.notifications.unblocked.title'))
            ->body(__('security::filament/pages/manage-blocked-ips.notifications.unblocked.body', [
                'ip' => $ip,
            ]))
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('refresh')
                ->label(__('security::filament/pages/manage-blocked-ips.actions.refresh'))
                ->icon('heroicon-o-arrow-path')
                ->action(fn (): mixed => $this->refreshBlockedIps()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        return [
            'enabled'            => (bool) config('security-login.enabled', true),
            'maxAttempts'        => (int) config('security-login.max_attempts', 5),
            'decayMinutes'       => (int) config('security-login.decay_minutes', 1),
            'blockAfterFailures' => (int) config('security-login.block_after_failures', 10),
            'failureWindow'      => (int) config('security-login.failure_window_minutes', 15),
            'blockMinutes'       => (int) config('security-login.block_minutes', 30),
            'whitelist'          => implode(', ', config('security-login.whitelist', [])),
        ];
    }
}
