<?php

namespace Webkul\Integrations;

use Filament\Contracts\Plugin;
use Filament\Panel;

class IntegrationPlugin implements Plugin
{
    public function getId(): string
    {
        return 'integrations';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->when($panel->getId() == 'admin', function (Panel $panel): void {
                $panel
                    ->discoverPages(
                        in: __DIR__.'/Filament/Clusters/Settings/Pages',
                        for: 'Webkul\\Integrations\\Filament\\Clusters\\Settings\\Pages'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
