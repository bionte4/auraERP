<?php

namespace Webkul\Project\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Throwable;
use Webkul\Contact\Filament\Resources\PartnerResource as ContactPartnerResource;
use Webkul\Partner\Models\Partner;
use Webkul\PluginManager\Package;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\MultiProjectCustomers as MultiProjectCustomersQuery;
use Webkul\Project\Services\OperationalScope;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Filament\NavigationBadge;

class MultiProjectCustomers extends Page implements HasActions, HasForms, HasTable
{
    use HasPageShield;
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?int $navigationSort = 2;

    protected static ?string $slug = 'project/multi-project-customers';

    protected string $view = 'projects::filament.pages.multi-project-customers';

    protected static function getPagePermission(): ?string
    {
        return 'page_project_multi_project_customers';
    }

    public static function canAccess(): bool
    {
        if (! Package::isPluginInstalled('projects')) {
            return false;
        }

        return parent::canAccess();
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/pages/multi-project-customers.navigation.title');
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return static::$navigationIcon;
    }

    public function getTitle(): string|Htmlable
    {
        return __('projects::filament/pages/multi-project-customers.title');
    }

    public function getHeading(): string|Htmlable
    {
        return __('projects::filament/pages/multi-project-customers.title');
    }

    public function getSubheading(): string|Htmlable|null
    {
        return __('projects::filament/pages/multi-project-customers.description');
    }

    public static function getNavigationBadge(): ?string
    {
        return NavigationBadge::count(
            'multi-project-customers',
            fn (): int => app(MultiProjectCustomersQuery::class)->count()
        );
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('projects::filament/pages/multi-project-customers.navigation.badge-tooltip');
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'info';
    }

    public function table(Table $table): Table
    {
        $canViewValue = app(OperationalScope::class)->canViewProjectValue();

        return $table
            ->query(fn (): Builder => app(MultiProjectCustomersQuery::class)->partnerQuery())
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/pages/multi-project-customers.columns.customer'))
                    ->weight(FontWeight::Bold)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('customer_projects_count')
                    ->label(__('projects::filament/pages/multi-project-customers.columns.projects'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('customer_projects_total_value')
                    ->label(__('projects::filament/pages/multi-project-customers.columns.total-value'))
                    ->visible($canViewValue)
                    ->formatStateUsing(function ($state): string {
                        $project = new Project(['project_value' => (float) $state]);

                        return $project->formattedProjectValue();
                    })
                    ->sortable(),
                TextColumn::make('project_names')
                    ->label(__('projects::filament/pages/multi-project-customers.columns.project-list'))
                    ->wrap()
                    ->state(function (Partner $record): string {
                        $names = $record->projects->pluck('name')->filter()->values();

                        if ($names->isEmpty()) {
                            return '—';
                        }

                        $shown = $names->take(8);

                        $extra = $names->count() - $shown->count();

                        return $shown->implode(', ').($extra > 0 ? ' +'.$extra : '');
                    }),
            ])
            ->defaultSort('customer_projects_count', 'desc')
            ->recordActions([
                Action::make('viewCustomer')
                    ->label(__('projects::filament/pages/multi-project-customers.actions.view-customer'))
                    ->icon('heroicon-o-eye')
                    ->url(fn (Partner $record): ?string => $this->customerUrl($record))
                    ->visible(fn (Partner $record): bool => filled($this->customerUrl($record))),
                Action::make('viewProjects')
                    ->label(__('projects::filament/pages/multi-project-customers.actions.view-projects'))
                    ->icon('heroicon-o-briefcase')
                    ->url(fn (Partner $record): string => ProjectResource::getUrl('index', [
                        'activeTableView' => 'customer_projects',
                        'tableSearch'     => $record->name,
                    ], panel: 'admin')),
            ])
            ->emptyStateHeading(__('projects::filament/pages/multi-project-customers.empty.heading'))
            ->emptyStateDescription(__('projects::filament/pages/multi-project-customers.empty.description'))
            ->striped();
    }

    protected function customerUrl(Partner $record): ?string
    {
        if (! class_exists(ContactPartnerResource::class)) {
            return null;
        }

        try {
            return ContactPartnerResource::getUrl('view', ['record' => $record], panel: 'admin');
        } catch (Throwable) {
            return null;
        }
    }
}
