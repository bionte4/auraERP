<?php

namespace Webkul\Pos;

use Filament\Panel;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Pos\Services\PosService;
use Webkul\Security\Services\RolePermissionSynchronizer;

class PosServiceProvider extends PackageServiceProvider
{
    public static string $name = 'pos';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasDependencies([
                'products',
                'inventories',
            ])
            ->hasMigrations([
                '2026_10_15_100000_create_pos_tables',
            ])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->endWith(function (): void {
                        if (Schema::hasTable('pos_registers') && Schema::hasTable('pos_payment_methods')) {
                            app(PosService::class)->ensureDefaultSetup();
                        }

                        if (Schema::hasTable('permissions') && Schema::hasTable('roles')) {
                            app(RolePermissionSynchronizer::class)->syncAll(false);
                        }
                    });
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {})
            ->icon('sales');
    }

    public function packageBooted(): void
    {
        $this->ensurePluginInstalledAndDefaultData();
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(PosPlugin::make());
        });
    }

    protected function ensurePluginInstalledAndDefaultData(): void
    {
        try {
            if (! Schema::hasTable('plugins')) {
                return;
            }

            $plugin = Plugin::query()->firstOrCreate(
                ['name' => static::$name],
                [
                    'author'         => 'auraERP',
                    'summary'        => 'Retail Point of Sale (POS) module with cashier terminal, shift management, stock deduction, multi-payment, and thermal receipts.',
                    'description'    => 'Retail Point of Sale (POS) module with cashier terminal, shift management, stock deduction, multi-payment, and thermal receipts.',
                    'latest_version' => '1.0.0',
                    'license'        => 'MIT',
                    'is_installed'   => true,
                    'is_active'      => true,
                ]
            );

            if (! $plugin->is_installed) {
                return;
            }

            if (Schema::hasTable('pos_registers') && Schema::hasTable('pos_payment_methods')) {
                app(PosService::class)->ensureDefaultSetup();
            }

            if (Schema::hasTable('permissions') && Schema::hasTable('roles')) {
                app(RolePermissionSynchronizer::class)->syncAll(false);
            }
        } catch (Throwable) {
            // Safely ignore during early boot / before database is ready.
        }
    }
}
