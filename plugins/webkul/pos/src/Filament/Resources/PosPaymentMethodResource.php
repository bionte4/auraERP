<?php

namespace Webkul\Pos\Filament\Resources;

use BackedEnum;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use Webkul\Pos\Filament\Resources\PosPaymentMethodResource\Pages\CreatePosPaymentMethod;
use Webkul\Pos\Filament\Resources\PosPaymentMethodResource\Pages\EditPosPaymentMethod;
use Webkul\Pos\Filament\Resources\PosPaymentMethodResource\Pages\ListPosPaymentMethods;
use Webkul\Pos\Models\PosPaymentMethod;
use Webkul\Support\Enums\NavigationGroup;

class PosPaymentMethodResource extends Resource
{
    protected static ?string $model = PosPaymentMethod::class;

    protected static ?string $slug = 'pos/payment-methods';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return NavigationGroup::Pos;
    }

    public static function getNavigationLabel(): string
    {
        return 'Metode Pembayaran POS';
    }

    public static function getModelLabel(): string
    {
        return 'Metode Pembayaran POS';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Metode Pembayaran POS';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Detail Metode Pembayaran')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Metode Pembayaran')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('code')
                        ->label('Kode')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50),
                    Select::make('type')
                        ->label('Tipe Pembayaran')
                        ->options(PosPaymentMethod::typeOptions())
                        ->default(PosPaymentMethod::TYPE_CASH)
                        ->required(),
                    TextInput::make('account_number')
                        ->label('Info Rekening / Merchant QRIS / EDC')
                        ->maxLength(255),
                    TextInput::make('sort')
                        ->label('Urutan Tampil')
                        ->numeric()
                        ->default(1),
                    Toggle::make('is_active')
                        ->label('Aktif')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort')
                    ->label('#')
                    ->sortable(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->badge()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Metode')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label('Tipe')
                    ->formatStateUsing(fn (string $state): string => PosPaymentMethod::typeOptions()[$state] ?? $state)
                    ->badge(),
                TextColumn::make('account_number')
                    ->label('Info Rekening / Merchant')
                    ->placeholder('-'),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('sort')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPosPaymentMethods::route('/'),
            'create' => CreatePosPaymentMethod::route('/create'),
            'edit'   => EditPosPaymentMethod::route('/{record}/edit'),
        ];
    }
}
