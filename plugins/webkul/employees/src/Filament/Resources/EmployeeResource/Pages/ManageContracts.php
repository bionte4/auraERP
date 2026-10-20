<?php

namespace Webkul\Employee\Filament\Resources\EmployeeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Models\EmployeeContract;

class ManageContracts extends ManageRelatedRecords
{
    protected static string $resource = EmployeeResource::class;

    protected static string $relationship = 'contracts';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/employee/pages/manage-contracts.navigation.title');
    }

    public function getTitle(): string
    {
        return __('employees::filament/resources/employee/pages/manage-contracts.navigation.title')
            .' — '.($this->record->name ?? '');
    }

    public static function statusOptions(): array
    {
        return [
            EmployeeContract::STATUS_ACTIVE     => __('employees::filament/resources/employee/pages/manage-contracts.status.active'),
            EmployeeContract::STATUS_COMPLETED  => __('employees::filament/resources/employee/pages/manage-contracts.status.completed'),
            EmployeeContract::STATUS_EXTENDED   => __('employees::filament/resources/employee/pages/manage-contracts.status.extended'),
            EmployeeContract::STATUS_CONVERTED  => __('employees::filament/resources/employee/pages/manage-contracts.status.converted'),
            EmployeeContract::STATUS_TERMINATED => __('employees::filament/resources/employee/pages/manage-contracts.status.terminated'),
        ];
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                Select::make('employment_type_id')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.employment-type'))
                    ->relationship('employmentType', 'name')
                    ->preload(),
                TextInput::make('contract_number')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.contract-number'))
                    ->maxLength(255),
                DatePicker::make('start_date')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.start-date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.end-date'))
                    ->afterOrEqual('start_date'),
                Select::make('status')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.status'))
                    ->options(static::statusOptions())
                    ->default(EmployeeContract::STATUS_ACTIVE)
                    ->required()
                    ->helperText(__('employees::filament/resources/employee/pages/manage-contracts.fields.status-helper-text')),
                Textarea::make('notes')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.notes'))
                    ->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sequence')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.columns.contract'))
                    ->formatStateUsing(fn ($state): string => __('employees::models/employee-contract.label', ['sequence' => $state]))
                    ->badge()
                    ->sortable(),
                TextColumn::make('employmentType.name')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.employment-type')),
                TextColumn::make('contract_number')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.contract-number'))
                    ->placeholder('—'),
                TextColumn::make('start_date')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.start-date'))
                    ->date(),
                TextColumn::make('end_date')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.end-date'))
                    ->date()
                    ->placeholder('—'),
                TextColumn::make('duration')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.columns.duration'))
                    ->state(fn (EmployeeContract $record): string => $record->durationInMonths().' '.__('employees::filament/resources/employee/pages/manage-contracts.columns.months')),
                TextColumn::make('status')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.fields.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state): string => static::statusOptions()[$state] ?? (string) $state)
                    ->color(fn ($state): string => match ($state) {
                        EmployeeContract::STATUS_ACTIVE => 'success',
                        EmployeeContract::STATUS_EXTENDED, EmployeeContract::STATUS_COMPLETED => 'gray',
                        EmployeeContract::STATUS_CONVERTED => 'info',
                        EmployeeContract::STATUS_TERMINATED => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('source')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.columns.source')),
            ])
            ->defaultSort('sequence')
            ->headerActions([
                CreateAction::make('extend')
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.actions.extend'))
                    ->icon('heroicon-o-arrow-path')
                    ->visible(fn (): bool => $this->record->contracts()->exists())
                    ->fillForm(function (): array {
                        $last = $this->record->contracts()->orderByDesc('sequence')->first();

                        return [
                            'employment_type_id' => $last?->employment_type_id,
                            'start_date'         => $last?->end_date?->copy()->addDay(),
                            'status'             => EmployeeContract::STATUS_ACTIVE,
                        ];
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['source'] = 'extension';

                        return $data;
                    }),
                CreateAction::make()
                    ->label(__('employees::filament/resources/employee/pages/manage-contracts.actions.create')),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
