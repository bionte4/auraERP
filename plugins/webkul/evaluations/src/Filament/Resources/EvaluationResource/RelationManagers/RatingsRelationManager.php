<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('evaluations::filament/resources/evaluation.ratings.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('evaluations::filament/resources/evaluation.ratings.model');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('evaluations::filament/resources/evaluation.ratings.form.title'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('weight')
                    ->label(__('evaluations::filament/resources/evaluation.ratings.form.weight'))
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->helperText(__('evaluations::filament/resources/evaluation.ratings.form.weight-helper')),
                TextInput::make('score')
                    ->label(__('evaluations::filament/resources/evaluation.ratings.form.score'))
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->helperText(__('evaluations::filament/resources/evaluation.ratings.form.score-helper')),
                Textarea::make('comment')
                    ->label(__('evaluations::filament/resources/evaluation.ratings.form.comment'))
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('evaluations::filament/resources/evaluation.ratings.form.title'))
                    ->searchable(),
                TextColumn::make('weight')
                    ->label(__('evaluations::filament/resources/evaluation.ratings.form.weight'))
                    ->suffix('%'),
                TextColumn::make('score')
                    ->label(__('evaluations::filament/resources/evaluation.ratings.form.score'))
                    ->formatStateUsing(fn (mixed $state): string => filled($state) ? (string) $state : '—'),
                TextColumn::make('comment')
                    ->label(__('evaluations::filament/resources/evaluation.ratings.form.comment'))
                    ->limit(40),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->headerActions([
                CreateAction::make()
                    ->icon('heroicon-o-plus-circle'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading(__('evaluations::filament/resources/evaluation.ratings.empty.heading'))
            ->emptyStateDescription(__('evaluations::filament/resources/evaluation.ratings.empty.description'));
    }
}
