<?php

namespace Webkul\Recruitment\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Panel;
use Webkul\Support\Enums\NavigationGroup;

class Applications extends Cluster
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static ?int $navigationSort = 2;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'recruitments/applications';
    }

    public static function getNavigationLabel(): string
    {
        return __('recruitments::filament/clusters/applications.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Recruitment;
    }
}
