<?php

namespace Webkul\Performance\Filament\Resources;

use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Performance\Filament\Resources\PerformancePlanResource\Pages\CreatePerformancePlan;
use Webkul\Performance\Filament\Resources\PerformancePlanResource\Pages\EditPerformancePlan;
use Webkul\Performance\Filament\Resources\PerformancePlanResource\Pages\ListPerformancePlans;
use Webkul\Performance\Filament\Resources\PerformancePlanResource\Pages\ViewPerformancePlan;
use Webkul\Performance\Filament\Resources\PerformancePlanResource\RelationManagers\GoalsRelationManager;
use Webkul\Performance\Filament\Resources\PerformancePlanResource\Schemas\PerformancePlanForm;
use Webkul\Performance\Filament\Resources\PerformancePlanResource\Schemas\PerformancePlanInfolist;
use Webkul\Performance\Filament\Resources\PerformancePlanResource\Tables\PerformancePlansTable;
use Webkul\Performance\Models\PerformancePlan;
use Webkul\Support\Enums\NavigationGroup;

class PerformancePlanResource extends Resource
{
    protected static ?string $model = PerformancePlan::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('performance::filament/resources/performance-plan.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }

    public static function getModelLabel(): string
    {
        return __('performance::filament/resources/performance-plan.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('performance::filament/resources/performance-plan.plural');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employee', 'goals']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'employee.name', 'summary'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->title;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('performance::filament/resources/performance-plan.table.columns.employee') => $record->employee?->name ?? '—',
            __('performance::filament/resources/performance-plan.table.columns.period')   => $record->periodLabel(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return PerformancePlanForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PerformancePlanInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PerformancePlansTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            GoalsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPerformancePlans::route('/'),
            'create' => CreatePerformancePlan::route('/create'),
            'view'   => ViewPerformancePlan::route('/{record}'),
            'edit'   => EditPerformancePlan::route('/{record}/edit'),
        ];
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'performance/plans';
    }
}
