<?php

namespace Webkul\Project\Filament\Resources\ProjectResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Models\Project;

class CustomerProjectsRelationManager extends RelationManager
{
    protected static string $relationship = 'projects';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('projects::filament/resources/project.navigation.title');
    }

    public function form(Schema $schema): Schema
    {
        return ProjectResource::form($schema);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/resources/project.table.columns.name'))
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),

                TextColumn::make('stage.name')
                    ->label(__('projects::filament/resources/project.infolist.sections.additional.entries.current-stage'))
                    ->badge(),

                TextColumn::make('user.name')
                    ->label(__('projects::filament/resources/project.table.columns.project-manager'))
                    ->placeholder('—'),

                TextColumn::make('start_date')
                    ->label(__('projects::filament/resources/project.table.columns.start-date'))
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('end_date')
                    ->label(__('projects::filament/resources/project.table.columns.end-date'))
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('—'),

                TextColumn::make('project_value')
                    ->label(__('projects::filament/resources/project.table.columns.project-value'))
                    ->formatStateUsing(fn (Project $record): string => $record->formattedProjectValue())
                    ->sortable(),

                TextColumn::make('expiry_reminder')
                    ->label(__('projects::filament/resources/project.table.columns.expiry-reminder'))
                    ->badge()
                    ->state(fn (Project $record): ?string => $record->expiryReminderStage())
                    ->color(fn (?string $state): string => match ($state) {
                        'expired', '1_month' => 'danger',
                        '2_months'           => 'warning',
                        '3_months'           => 'info',
                        default              => 'gray',
                    })
                    ->formatStateUsing(function (?string $state, Project $record): string {
                        $days = $record->daysUntilEndDate();

                        return match ($state) {
                            'expired'  => __('projects::filament/resources/project.notifications.stages.expired'),
                            '1_month'  => __('projects::filament/resources/project.notifications.stages.1_month')." ({$days}d)",
                            '2_months' => __('projects::filament/resources/project.notifications.stages.2_months')." ({$days}d)",
                            '3_months' => __('projects::filament/resources/project.notifications.stages.3_months')." ({$days}d)",
                            default    => '—',
                        };
                    }),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label(__('projects::filament/resources/project/pages/list-projects.header-actions.create.label'))
                    ->icon('heroicon-o-plus-circle')
                    ->mutateDataUsing(function (array $data): array {
                        $data['partner_id'] = $this->getOwnerRecord()->id;
                        $data['creator_id'] ??= Auth::id();
                        $data['company_id'] ??= $this->getOwnerRecord()->company_id ?? current_company_id();

                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Project $record): string => ProjectResource::getUrl('view', ['record' => $record])),
                EditAction::make(),
            ]);
    }
}
