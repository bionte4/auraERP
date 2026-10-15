<?php

namespace Webkul\Evaluation;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class EvaluationServiceProvider extends PackageServiceProvider
{
    public static string $name = 'evaluations';

    public static string $viewNamespace = 'evaluations';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasDependencies([
                'employees',
            ])
            ->hasMigrations([
                '2026_10_14_150000_create_evaluations_evaluations_table',
                '2026_10_14_150001_create_evaluations_ratings_table',
                '2026_10_15_140000_add_pkwt_quarterly_fields_to_evaluations_tables',
            ])
            ->runsMigrations()
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {})
            ->icon('employees');
    }

    public function packageBooted(): void
    {
        //
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(EvaluationPlugin::make());
        });
    }
}
