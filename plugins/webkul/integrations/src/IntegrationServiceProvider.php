<?php

namespace Webkul\Integrations;

use Filament\Panel;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Models\Plugin;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;
use Webkul\Security\Services\RolePermissionSynchronizer;

class IntegrationServiceProvider extends PackageServiceProvider
{
    public static string $name = 'integrations';

    public static string $viewNamespace = 'integrations';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->isCore()
            ->hasTranslations()
            ->hasSettings([
                '2026_10_16_120000_create_integration_settings',
            ])
            ->runsSettings()
            ->hasInstallCommand(function (InstallCommand $command): void {
                $command
                    ->runsMigrations()
                    ->endWith(function (): void {
                        if (Schema::hasTable('permissions') && Schema::hasTable('roles')) {
                            app(RolePermissionSynchronizer::class)->syncAll(false);
                        }
                    });
            })
            ->hasUninstallCommand(function (UninstallCommand $command): void {})
            ->icon('settings');
    }

    public function packageBooted(): void
    {
        $this->ensurePluginMarkedInstalled();
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(IntegrationsPlugin::make());
        });
    }

    protected function ensurePluginMarkedInstalled(): void
    {
        try {
            if (! Schema::hasTable('plugins')) {
                return;
            }

            Plugin::query()->firstOrCreate(
                ['name' => static::$name],
                [
                    'author'         => 'auraERP',
                    'summary'        => 'SMTP, auraAI, and Jira integrations',
                    'description'    => 'SMTP, auraAI, and Jira connection settings with test buttons',
                    'latest_version' => '1.0.0',
                    'license'        => 'MIT',
                    'is_active'      => true,
                    'is_installed'   => true,
                    'sort'           => 50,
                ]
            )->update(['is_installed' => true, 'is_active' => true]);
        } catch (Throwable) {
            // Database may be unavailable during early bootstrap.
        }
    }
}
