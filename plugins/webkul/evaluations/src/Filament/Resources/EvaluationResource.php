<?php

namespace Webkul\Evaluation\Filament\Resources;

use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages\CreateEvaluation;
use Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages\EditEvaluation;
use Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages\ListEvaluations;
use Webkul\Evaluation\Filament\Resources\EvaluationResource\Pages\ViewEvaluation;
use Webkul\Evaluation\Filament\Resources\EvaluationResource\RelationManagers\RatingsRelationManager;
use Webkul\Evaluation\Filament\Resources\EvaluationResource\Schemas\EvaluationForm;
use Webkul\Evaluation\Filament\Resources\EvaluationResource\Schemas\EvaluationInfolist;
use Webkul\Evaluation\Filament\Resources\EvaluationResource\Tables\EvaluationsTable;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Support\Enums\NavigationGroup;

class EvaluationResource extends Resource
{
    protected static ?string $model = Evaluation::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('evaluations::filament/resources/evaluation.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }

    public static function getModelLabel(): string
    {
        return __('evaluations::filament/resources/evaluation.model');
    }

    public static function getPluralModelLabel(): string
    {
        return __('evaluations::filament/resources/evaluation.plural');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['employee', 'reviewer', 'ratings']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'employee.name', 'reviewer.name', 'summary'];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->title;
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('evaluations::filament/resources/evaluation.table.columns.employee') => $record->employee?->name ?? '—',
            __('evaluations::filament/resources/evaluation.table.columns.period')   => $record->periodLabel(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return EvaluationForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EvaluationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EvaluationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RatingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListEvaluations::route('/'),
            'create' => CreateEvaluation::route('/create'),
            'view'   => ViewEvaluation::route('/{record}'),
            'edit'   => EditEvaluation::route('/{record}/edit'),
        ];
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'evaluations';
    }
}
