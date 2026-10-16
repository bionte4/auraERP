<?php

namespace Webkul\Employee\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Panel;
use Webkul\Support\Enums\NavigationGroup;

class Configurations extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 6;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'employees/configurations';
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/clusters/configurations.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }
}
