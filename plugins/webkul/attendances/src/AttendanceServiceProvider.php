<?php

namespace Webkul\Attendance;

use Filament\Panel;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class AttendanceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'attendances';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasDependencies([
                'employees',
            ])
            ->hasMigrations([
                '2026_10_14_100000_create_attendances_attendances_table',
                '2026_10_14_120000_add_location_to_attendances_attendances_table',
                '2026_10_14_130000_create_attendances_face_profiles_table',
                '2026_10_14_140000_create_attendances_tracks_table',
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
            $panel->plugin(AttendancePlugin::make());
        });
    }
}
