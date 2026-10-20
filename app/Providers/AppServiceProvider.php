<?php

namespace App\Providers;

use App\Http\Responses\FilamentLoginResponse;
use Filament\Auth\Http\Responses\Contracts\LoginResponse as LoginResponseContract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Livewire\Component;
use Livewire\Livewire;
use Webkul\Security\Models\User;

use function Livewire\on;
use function Livewire\store;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(Authenticatable::class, User::class);
        $this->app->bind(LoginResponseContract::class, FilamentLoginResponse::class);
    }

    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        // Filament Alpine selects (x-load) use absolute asset() URLs. When the app is
        // served on both auraerp.my.id and opsora.my.id, a mismatched APP_URL makes
        // cross-origin module loads fail → empty dropdowns site-wide. Keep assets
        // same-origin with the current request host.
        // Note: asset() prefers config('app.asset_url') over the forced root URL,
        // and that config falls back to APP_URL — so the asset root must be
        // pinned to the request host as well, otherwise JS/CSS/fonts are still
        // served from the APP_URL domain and blocked by CORS (checkbox lists
        // and tabs then never initialise on the other domain).
        if ((! app()->runningInConsole()) && filled(request()->getHost())) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
            URL::useAssetOrigin(request()->getSchemeAndHttpHost());
        }

        on('dehydrate', function (Component $component): void {
            if (! Livewire::isLivewireRequest()) {
                return;
            }

            if (! store($component)->has('redirect')) {
                return;
            }

            $notifications = session()->pull('filament.notifications');

            if (empty($notifications)) {
                return;
            }

            session()->put('filament.claimed_notifications', $notifications);
        });
    }
}
