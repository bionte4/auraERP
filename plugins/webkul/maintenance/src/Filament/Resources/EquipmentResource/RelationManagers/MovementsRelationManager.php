<?php

namespace Webkul\Maintenance\Filament\Resources\EquipmentResource\RelationManagers;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Maintenance\Enums\EquipmentMovementType;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Project\Models\Project;

class MovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'movements';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('maintenance::filament/resources/equipment.movements.title');
    }

    protected static function getModelLabel(): ?string
    {
        return __('maintenance::filament/resources/equipment.movements.model');
    }

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('movement_type')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.movement-type'))
                    ->options(EquipmentMovementType::options())
                    ->default(EquipmentMovementType::Relocation->value)
                    ->required()
                    ->live(),

                DatePicker::make('movement_date')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.movement-date'))
                    ->default(now())
                    ->required()
                    ->native(false),

                TextInput::make('reference')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.reference'))
                    ->placeholder('BAST / DO / Ticket #')
                    ->maxLength(255),

                Select::make('replacement_equipment_id')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.replacement-equipment'))
                    ->relationship(
                        'replacementEquipment',
                        'name',
                        modifyQueryUsing: fn (Builder $query) => $query->whereKeyNot($this->getOwnerRecord()->getKey()),
                    )
                    ->preload()
                    ->visible(fn (Get $get): bool => $get('movement_type') === EquipmentMovementType::Replacement->value)
                    ->required(fn (Get $get): bool => $get('movement_type') === EquipmentMovementType::Replacement->value),

                Select::make('to_employee_id')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.to-employee'))
                    ->relationship('toEmployee', 'name')
                    ->default(fn (): ?int => $this->getOwnerRecord()->employee_id)
                    ->preload()
                    ,

                Select::make('to_project_id')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.to-project'))
                    ->relationship('toProject', 'name')
                    ->default(fn (): ?int => $this->getOwnerRecord()->project_id)
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set, Get $get, mixed $state): void {
                        if (! $state || $get('to_customer_id')) {
                            return;
                        }

                        $partnerId = Project::withoutGlobalScopes()->find($state)?->partner_id;

                        if ($partnerId) {
                            $set('to_customer_id', $partnerId);
                        }
                    }),

                Select::make('to_customer_id')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.to-customer'))
                    ->relationship(
                        'toCustomer',
                        'name',
                        modifyQueryUsing: fn (Builder $query, $state): Builder => Equipment::filterExternalPartnersQuery($query, 'customer', $state),
                    )
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label(__('maintenance::filament/resources/equipment.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                    ])
                    ->createOptionUsing(fn (array $data): int => Equipment::createExternalPartner($data, 'customer'))
                    ->default(fn (): ?int => $this->getOwnerRecord()->customer_id)
                    ->preload()
                    ,

                TextInput::make('to_location')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.to-location'))
                    ->default(fn (): ?string => $this->getOwnerRecord()->location)
                    ->maxLength(255),

                Textarea::make('notes')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.notes'))
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('movement_date')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.movement-date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('movement_type')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.movement-type'))
                    ->badge(),

                TextColumn::make('reference')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.reference'))
                    ->placeholder('—')
                    ->searchable(),

                TextColumn::make('fromEmployee.name')
                    ->label(__('maintenance::filament/resources/equipment.movements.columns.from-employee'))
                    ->placeholder('—'),

                TextColumn::make('toEmployee.name')
                    ->label(__('maintenance::filament/resources/equipment.movements.columns.to-employee'))
                    ->placeholder('—'),

                TextColumn::make('fromProject.name')
                    ->label(__('maintenance::filament/resources/equipment.movements.columns.from-project'))
                    ->placeholder('—'),

                TextColumn::make('toProject.name')
                    ->label(__('maintenance::filament/resources/equipment.movements.columns.to-project'))
                    ->placeholder('—'),

                TextColumn::make('from_location')
                    ->label(__('maintenance::filament/resources/equipment.movements.columns.from-location'))
                    ->placeholder('—'),

                TextColumn::make('to_location')
                    ->label(__('maintenance::filament/resources/equipment.movements.columns.to-location'))
                    ->placeholder('—'),

                TextColumn::make('replacementEquipment.name')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.replacement-equipment'))
                    ->placeholder('—'),

                TextColumn::make('notes')
                    ->label(__('maintenance::filament/resources/equipment.movements.fields.notes'))
                    ->limit(50)
                    ->placeholder('—'),
            ])
            ->defaultSort('movement_date', 'desc')
            ->headerActions([
                CreateAction::make()
                    ->label(__('maintenance::filament/resources/equipment.movements.create-button'))
                    ->icon('heroicon-o-arrows-right-left'),
            ])
            ->recordActions([
                DeleteAction::make(),
            ]);
    }
}
