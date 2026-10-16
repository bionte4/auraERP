<?php

namespace Webkul\Security\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Webkul\Security\Filament\Resources\AuditTrailResource\Pages\ListAuditTrails;
use Webkul\Security\Filament\Resources\AuditTrailResource\Pages\ViewAuditTrail;
use Webkul\Security\Filament\Resources\AuditTrailResource\Schemas\AuditTrailInfolist;
use Webkul\Security\Filament\Resources\AuditTrailResource\Tables\AuditTrailsTable;
use Webkul\Security\Models\AuditTrail;
use Webkul\Security\Support\AdminAccess;
use Webkul\Support\Enums\NavigationGroup;

class AuditTrailResource extends Resource
{
    protected static ?string $model = AuditTrail::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shield-exclamation';

    protected static ?int $navigationSort = 8;

    protected static ?string $slug = 'audit-trail';

    public static function getNavigationLabel(): string
    {
        return __('security::filament/resources/audit-trail.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('security::filament/resources/audit-trail.title');
    }

    public static function getPluralModelLabel(): string
    {
        return __('security::filament/resources/audit-trail.plural');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Setting;
    }

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
        ];
    }

    public static function canAccess(): bool
    {
        return AdminAccess::check();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return AdminAccess::check();
    }

    public static function canViewAny(): bool
    {
        return AdminAccess::check();
    }

    public static function canView(Model $record): bool
    {
        return AdminAccess::check();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canForceDelete(Model $record): bool
    {
        return false;
    }

    public static function canForceDeleteAny(): bool
    {
        return false;
    }

    public static function canRestore(Model $record): bool
    {
        return false;
    }

    public static function canRestoreAny(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return AuditTrailsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AuditTrailInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAuditTrails::route('/'),
            'view'  => ViewAuditTrail::route('/{record}'),
        ];
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-shield-exclamation';
    }
}
