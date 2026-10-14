<?php

namespace Webkul\Performance\Filament\Resources\PerformancePlanResource\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Performance\Enums\GoalStatus;

class GoalsRelationManager extends RelationManager
{
    protected static string $relationship = 'goals';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('performance::filament/resources/performance-plan.goals.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('performance::filament/resources/performance-plan.goals.model');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.title'))
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('metric')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.metric'))
                    ->maxLength(255),
                TextInput::make('target')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.target'))
                    ->maxLength(255),
                TextInput::make('weight')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.weight'))
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->helperText(__('performance::filament/resources/performance-plan.goals.form.weight-helper')),
                TextInput::make('progress')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.progress'))
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(0)
                    ->suffix('%'),
                Select::make('status')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.status'))
                    ->options(GoalStatus::options())
                    ->required()
                    ->default(GoalStatus::NotStarted->value),
                Textarea::make('notes')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.notes'))
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.title'))
                    ->searchable(),
                TextColumn::make('metric')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.metric')),
                TextColumn::make('target')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.target')),
                TextColumn::make('weight')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.weight'))
                    ->suffix('%'),
                TextColumn::make('progress')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.progress'))
                    ->suffix('%'),
                TextColumn::make('status')
                    ->label(__('performance::filament/resources/performance-plan.goals.form.status'))
                    ->badge(),
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
            ->emptyStateHeading(__('performance::filament/resources/performance-plan.goals.empty.heading'))
            ->emptyStateDescription(__('performance::filament/resources/performance-plan.goals.empty.description'));
    }
}
