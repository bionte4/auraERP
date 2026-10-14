<?php

namespace App\Providers\Filament;

use App\Http\Middleware\ApplyBrandSettings;
use App\Http\Middleware\SetLocale;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class CustomerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('customer')
            ->path('/')
            ->homeUrl(url('/'))
            ->authPasswordBroker('customers')
            ->profile(isSimple: false)
            ->favicon(asset('images/favicon.ico'))
            ->brandLogo(asset('images/logo-full-light.svg'))
            ->darkModeBrandLogo(asset('images/logo-full-dark.svg'))
            ->darkMode(false)
            ->brandLogoHeight('2rem')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->topNavigation()
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_END,
                fn () => view('filament.components.language-switcher'),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="'.e(asset('css/aura/density.css')).'?v='.filemtime(public_path('css/aura/density.css')).'">',
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                SetLocale::class,
                ApplyBrandSettings::class,
            ])
            ->authGuard('customer');
    }
}
