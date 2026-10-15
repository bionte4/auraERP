<?php

namespace Webkul\Pos;

use Filament\Contracts\Plugin;
use Filament\Panel;

class PosPlugin implements Plugin
{
    public function getId(): string
    {
        return 'pos';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        $panel
            ->when($panel->getId() === 'admin', function (Panel $panel): void {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Resources',
                        for: 'Webkul\\Pos\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Pos\\Filament\\Pages'
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
