<?php

namespace Webkul\Security\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Security\Filament\Resources\TeamResource\Pages\ManageTeams;
use Webkul\Security\Filament\Resources\TeamResource\Schemas\TeamForm;
use Webkul\Security\Filament\Resources\TeamResource\Schemas\TeamInfolist;
use Webkul\Security\Filament\Resources\TeamResource\Tables\TeamsTable;
use Webkul\Security\Models\Team;
use Webkul\Support\Enums\NavigationGroup;

class TeamResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $model = Team::class;

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->ownership();

        // Operational scope: non-Admin/HRD users only see teams of the
        // projects assigned to them (managed or member), teams they
        // created, and teams they belong to. Admin/HRD stay unscoped.
        $scopeClass = 'Webkul\\Project\\Services\\OperationalScope';

        if (! auth()->check() || ! class_exists($scopeClass)) {
            return $query;
        }

        $scope = app($scopeClass);
        $user = auth()->user();

        if ($scope->isUnscoped($user)) {
            return $query;
        }

        $teamIds = $scope->visibleTeamIds($user);

        if ($teamIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn($query->getModel()->getTable().'.id', $teamIds);
    }

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/team.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function form(Schema $schema): Schema
    {
        return TeamForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TeamsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return TeamInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageTeams::route('/'),
        ];
    }
}
