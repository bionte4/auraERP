<?php

namespace Webkul\Pos\Filament\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Pos\Filament\Pages\PosTerminalPage;
use Webkul\Pos\Filament\Resources\PosRegisterResource\Pages\CreatePosRegister;
use Webkul\Pos\Filament\Resources\PosRegisterResource\Pages\EditPosRegister;
use Webkul\Pos\Filament\Resources\PosRegisterResource\Pages\ListPosRegisters;
use Webkul\Pos\Models\PosRegister;
use Webkul\Support\Enums\NavigationGroup;

class PosRegisterResource extends Resource
{
    protected static ?string $model = PosRegister::class;

    protected static ?string $slug = 'pos/registers';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return NavigationGroup::Pos;
    }

    public static function getNavigationLabel(): string
    {
        return 'Mesin Kasir (Register)';
    }

    public static function getModelLabel(): string
    {
        return 'Mesin Kasir';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mesin Kasir (Register)';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Konfigurasi Mesin Kasir & Gudang')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Nama Mesin Kasir')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('code')
                        ->label('Kode Register')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(50),
                    Select::make('warehouse_id')
                        ->label('Gudang Utama (Warehouse)')
                        ->options(fn (): array => Warehouse::withTrashed()->withoutGlobalScopes()->pluck('name', 'id')->all())
                        ->preload(),
                    Select::make('stock_location_id')
                        ->label('Lokasi Potong Stok (Stock Location)')
                        ->options(fn (): array => Location::withTrashed()->withoutGlobalScopes()->where('type', 'internal')->pluck('full_name', 'id')->all())
                        ->preload(),
                    TextInput::make('default_tax_rate')
                        ->label('Pajak Default / PPN (%)')
                        ->numeric()
                        ->default(0)
                        ->minValue(0)
                        ->maxValue(100),
                    Select::make('receipt_paper_size')
                        ->label('Ukuran Kertas Struk Thermal')
                        ->options([
                            '80mm' => 'Thermal 80mm (Standar)',
                            '58mm' => 'Thermal 58mm (Mini/Portable)',
                        ])
                        ->default('80mm')
                        ->required(),
                    Toggle::make('auto_deduct_stock')
                        ->label('Potong Stok Gudang Otomatis Saat Checkout')
                        ->default(true),
                    Toggle::make('allow_out_of_stock_sale')
                        ->label('Izinkan Jual Saat Stok 0 / Minus')
                        ->default(true),
                    Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->default(true),
                ]),
            Section::make('Template Struk Thermal')
                ->columns(2)
                ->schema([
                    Textarea::make('receipt_header')
                        ->label('Header Struk (Alamat / Telepon Toko)')
                        ->rows(3),
                    Textarea::make('receipt_footer')
                        ->label('Footer Struk (Ucapan Terima Kasih)')
                        ->rows(3),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable()
                    ->badge(),
                TextColumn::make('name')
                    ->label('Nama Mesin Kasir')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('warehouse.name')
                    ->label('Gudang')
                    ->placeholder('-'),
                TextColumn::make('stockLocation.full_name')
                    ->label('Lokasi Stok')
                    ->placeholder('-'),
                TextColumn::make('receipt_paper_size')
                    ->label('Kertas Struk')
                    ->badge(),
                IconColumn::make('auto_deduct_stock')
                    ->label('Potong Stok')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->recordActions([
                Action::make('open_terminal')
                    ->label('Buka Kasir')
                    ->icon('heroicon-o-calculator')
                    ->color('success')
                    ->url(fn (PosRegister $record): string => PosTerminalPage::getUrl(['register_id' => $record->id])),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListPosRegisters::route('/'),
            'create' => CreatePosRegister::route('/create'),
            'edit'   => EditPosRegister::route('/{record}/edit'),
        ];
    }
}
