<?php

namespace App\Providers\Filament;

use App\Http\Middleware\ApplyBrandSettings;
use App\Http\Middleware\SetLocale;
use App\Http\Responses\FilamentLoginResponse;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Actions\Action;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup as FilamentNavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use LaraZeus\SpatieTranslatable\SpatieTranslatablePlugin;
use Webkul\Security\Filament\Auth\Login;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\Pages\Profile;
use Webkul\Support\GlobalSearchProvider;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        if (! app()->runningInConsole()) {
            set_time_limit(300);
        }

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(Login::class)
            ->homeUrl(fn (): string => FilamentLoginResponse::homeUrl())
            ->favicon(asset('images/favicon.ico'))
            ->brandLogo(asset('images/logo-full-light.svg'))
            ->darkModeBrandLogo(asset('images/logo-full-dark.svg'))
            ->brandLogoHeight('2rem')
            ->passwordReset()
            ->profile()
            ->colors([
                'primary' => Color::Indigo,
                'gray'    => Color::Slate,
            ])
            ->unsavedChangesAlerts()
            // Sidebar stays expanded with labels on desktop (no icon-only
            // rail): labels + navigation badges must remain readable.
            // Mobile keeps the off-canvas drawer behaviour by default.
            ->sidebarWidth('15.5rem')
            ->renderHook(
                PanelsRenderHook::TOPBAR_START,
                fn () => view('filament.components.topbar-left'),
            )
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn () => view('filament.components.topbar-utilities'),
            )
            ->renderHook(
                PanelsRenderHook::SIMPLE_LAYOUT_START,
                fn () => filament()->auth()->check()
                    ? view('filament.components.language-switcher')
                    : '',
            )
            ->renderHook(
                PanelsRenderHook::SIMPLE_PAGE_START,
                fn () => filament()->auth()->check()
                    ? ''
                    : view('filament.components.language-switcher', ['compact' => true]),
            )
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => '<link rel="stylesheet" href="'.e(asset('css/aura/density.css')).'?v='.filemtime(public_path('css/aura/density.css')).'">'
                    .'<link rel="stylesheet" href="'.e(asset('css/aura/modern-theme.css')).'?v='.filemtime(public_path('css/aura/modern-theme.css')).'">',
            )
            ->maxContentWidth(Width::Full)
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->userMenuItems([
                'profile' => Action::make('profile')
                    ->label(fn () => Auth::user()?->name)
                    ->url(fn (): string => Profile::getUrl()),
            ])
            ->navigationGroups(
                collect(NavigationGroup::cases())->mapWithKeys(
                    fn (NavigationGroup $case) => [
                        $case->name => FilamentNavigationGroup::make()
                            ->label(fn () => $case->getLabel())
                            ->icon(fn () => $case->getIcon())
                            ->collapsible(),
                    ]
                )->all()
            )
            ->plugins([
                FilamentShieldPlugin::make()
                    ->gridColumns([
                        'default' => 1,
                        'sm'      => 1,
                        'lg'      => 2,
                        'xl'      => 3,
                    ])
                    ->sectionColumnSpan(1)
                    ->checkboxListColumns([
                        'default' => 1,
                        'sm'      => 1,
                        'lg'      => 2,
                        'xl'      => 3,
                    ])
                    ->resourceCheckboxListColumns([
                        'default' => 1,
                        'sm'      => 2,
                    ]),
                SpatieTranslatablePlugin::make()
                    ->defaultLocales(array_keys(config('app.supported_locales'))),
            ])
            ->globalSearch(provider: GlobalSearchProvider::class)
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
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
