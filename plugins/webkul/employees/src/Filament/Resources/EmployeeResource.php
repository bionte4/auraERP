<?php

namespace Webkul\Employee\Filament\Resources;

use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Panel;
use Filament\Resources\Pages\Page;
use Filament\Resources\RelationManagers\RelationGroup;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\CreateEmployee;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\EditEmployee;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ListEmployees;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageAssignedProjects;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageContracts;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageOverview;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageResume;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ManageSkill;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ViewEmployee;
use Webkul\Employee\Filament\Resources\EmployeeResource\RelationManagers\ResumeRelationManager;
use Webkul\Employee\Filament\Resources\EmployeeResource\RelationManagers\SkillsRelationManager;
use Webkul\Employee\Filament\Resources\EmployeeResource\Schemas\EmployeeForm;
use Webkul\Employee\Filament\Resources\EmployeeResource\Schemas\EmployeeInfolist;
use Webkul\Employee\Filament\Resources\EmployeeResource\Tables\EmployeesTable;
use Webkul\Employee\Models\Employee;
use Webkul\Field\Filament\Traits\HasCustomFields;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\NavigationBadge;

class EmployeeResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

    use HasCustomFields;

    protected static ?string $model = Employee::class;

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?int $navigationSort = 1;

    public static function getModelLabel(): string
    {
        return __('employees::filament/resources/employee.title');
    }

    public static function getNavigationLabel(): string
    {
        return __('employees::filament/resources/employee.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Employee;
    }

    public static function getNavigationBadge(): ?string
    {
        return NavigationBadge::count('employees-expiring-contracts', fn (): int => Employee::query()
            ->where('is_active', true)
            ->whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', '<=', now()->addDays(30)->toDateString())
            ->count());
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('employees::filament/resources/employee.navigation.badge-tooltip');
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $hasExpired = NavigationBadge::value('employees-expired-contracts', fn (): int => Employee::query()
            ->where('is_active', true)
            ->whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', '<', now()->toDateString())
            ->count()) > 0;

        return $hasExpired ? 'danger' : 'warning';
    }

    /**
     * Scoped users (e.g. Operation Manager) only see/manage employees in
     * their operational scope: employees on their projects, themselves,
     * their direct reports, and employees they created. Admin/HRD and
     * console contexts stay unscoped.
     */
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (\Illuminate\Support\Facades\Schema::hasTable('employees_employee_contracts')) {
            $query->withCount('contracts')->withMax('contracts', 'sequence');
        }

        $scopeClass = 'Webkul\\Project\\Services\\OperationalScope';

        if (! auth()->check() || ! class_exists($scopeClass)) {
            return $query;
        }

        $scope = app($scopeClass);

        if ($scope->isUnscoped()) {
            return $query;
        }

        $employeeIds = $scope->manageableEmployeeIds()->all();

        if ($employeeIds === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereIn('employees_employees.id', $employeeIds);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'name',
            'department.name',
            'work_email',
            'work_phone',
        ];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('employees::filament/resources/employee.global-search.department') => $record?->department?->name ?? '—',
            __('employees::filament/resources/employee.global-search.work-email') => $record?->work_email ?? '—',
            __('employees::filament/resources/employee.global-search.work-phone') => $record?->work_phone ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return EmployeeForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return EmployeesTable::configure($table, static::getCustomTableColumns(), static::getCustomTableFilters());
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmployeeInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewEmployee::class,
            EditEmployee::class,
            ManageOverview::class,
            ManageSkill::class,
            ManageResume::class,
            ManageAssignedProjects::class,
            ManageContracts::class,
        ]);
    }

    public static function getRelations(): array
    {
        $relations = [
            RelationGroup::make('Manage Skills', [
                SkillsRelationManager::class,
            ])
                ->icon('heroicon-o-bolt'),
            RelationGroup::make('Manage Resumes', [
                ResumeRelationManager::class,
            ])
                ->icon('heroicon-o-clipboard-document-list'),
        ];

        return $relations;
    }

    public static function getSlug(?Panel $panel = null): string
    {
        return 'employees/employees';
    }

    public static function getPages(): array
    {
        return [
            'index'             => ListEmployees::route('/'),
            'create'            => CreateEmployee::route('/create'),
            'edit'              => EditEmployee::route('/{record}/edit'),
            'view'              => ViewEmployee::route('/{record}'),
            'overview'          => ManageOverview::route('/{record}/overview'),
            'skills'            => ManageSkill::route('/{record}/skills'),
            'resumes'           => ManageResume::route('/{record}/resumes'),
            'assigned-projects' => ManageAssignedProjects::route('/{record}/assigned-projects'),
            'contracts'         => ManageContracts::route('/{record}/contracts'),
        ];
    }
}
