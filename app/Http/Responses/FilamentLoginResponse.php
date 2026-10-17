<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Webkul\Support\Filament\Pages\MyDayDashboard;
use Webkul\Support\Filament\Pages\WelcomeDashboard;

class FilamentLoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        self::forgetUnsafeIntendedUrl();

        $intended = session()->pull('url.intended');

        if (is_string($intended) && $intended !== '' && ! self::isUnsafeAuthUrl($intended)) {
            return redirect()->to($intended);
        }

        return redirect()->to(self::homeUrl());
    }

    public static function homeUrl(): string
    {
        $panelId = Filament::getCurrentPanel()?->getId() ?? 'admin';

        if (WelcomeDashboard::canAccess()) {
            return WelcomeDashboard::getUrl(panel: $panelId);
        }

        if (MyDayDashboard::canAccess()) {
            return MyDayDashboard::getUrl(panel: $panelId);
        }

        return Filament::getUrl() ?? url('/admin');
    }

    public static function forgetUnsafeIntendedUrl(): void
    {
        $intended = session()->get('url.intended');

        if (! is_string($intended) || $intended === '') {
            return;
        }

        if (self::isUnsafeAuthUrl($intended)) {
            session()->forget('url.intended');
        }
    }

    public static function isUnsafeAuthUrl(string $url): bool
    {
        $path = parse_url($url, PHP_URL_PATH) ?? '';
        $path = '/'.trim($path, '/');

        return str_contains($path, '/login')
            || str_contains($path, '/logout')
            || str_contains($path, '/password-reset')
            || str_contains($path, '/password/reset')
            || str_contains($path, '/auth/');
    }
}
