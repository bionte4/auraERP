<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\RelationManagers;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Evaluation\Models\EvaluationRating;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    protected static ?string $recordTitleAttribute = 'title';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return 'D. Manajemen Pekerjaan (10 Aspek Penilaian & Komentar)';
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
                    ->label('Aspek Penilaian')
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                Textarea::make('description')
                    ->label('Deskripsi Standar Aspek')
                    ->rows(2)
                    ->columnSpanFull(),
                TextInput::make('weight')
                    ->label(__('evaluations::filament/resources/evaluation.ratings.form.weight'))
                    ->numeric()
                    ->required()
                    ->minValue(0)
                    ->maxValue(100)
                    ->default(10)
                    ->helperText(__('evaluations::filament/resources/evaluation.ratings.form.weight-helper')),
                TextInput::make('score')
                    ->label('Rating (Skala 1 - 5)')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(5)
                    ->helperText('5: Konsisten melampaui | 4: Signifikan melebihi | 3: Memenuhi standar | 2: Kurang | 1: Tidak memenuhi'),
                Textarea::make('comment')
                    ->label('Komentar / Justifikasi Penilai')
                    ->placeholder('Tuliskan justifikasi dan contoh perilaku/pencapaian karyawan pada aspek ini...')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort')
                    ->label('No.')
                    ->width('50px')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Aspek Penilaian')
                    ->description(fn (EvaluationRating $record): ?string => $record->description)
                    ->wrap()
                    ->searchable(),
                TextColumn::make('weight')
                    ->label(__('evaluations::filament/resources/evaluation.ratings.form.weight'))
                    ->suffix('%')
                    ->toggleable(),
                TextColumn::make('score')
                    ->label('Rating (1-5)')
                    ->badge()
                    ->color(fn (?int $state): string => match (true) {
                        $state === null => 'gray',
                        $state >= 4     => 'success',
                        $state === 3    => 'info',
                        default         => 'danger',
                    })
                    ->formatStateUsing(fn (mixed $state): string => filled($state) ? (string) $state : '—'),
                TextColumn::make('comment')
                    ->label('Komentar Justifikasi')
                    ->wrap()
                    ->limit(80),
            ])
            ->defaultSort('sort')
            ->reorderable('sort')
            ->headerActions([
                Action::make('seed_pkwt_defaults')
                    ->label('Muat 10 Aspek Standar PKWT')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->action(function (): void {
                        /** @var Evaluation $evaluation */
                        $evaluation = $this->getOwnerRecord();
                        $added = $evaluation->seedDefaultPkwtCriteria(false);

                        Notification::make()
                            ->success()
                            ->title('Aspek Standar PKWT Dimuat')
                            ->body("{$added} aspek manajemen pekerjaan standar PKWT berhasil ditambahkan.")
                            ->send();
                    }),
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
