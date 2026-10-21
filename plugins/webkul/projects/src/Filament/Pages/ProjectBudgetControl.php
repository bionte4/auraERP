<?php

namespace Webkul\Project\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Project\Services\ProjectBudgetControlData;
use Webkul\Security\Models\User;
use Webkul\Security\Support\AdminAccess;
use Webkul\Support\Enums\NavigationGroup;

class ProjectBudgetControl extends Page
{
    use HasPageShield;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?int $navigationSort = 5;

    protected static ?string $slug = 'project/budget-control';

    protected string $view = 'projects::filament.pages.project-budget-control';

    public string $search = '';

    public string $statusFilter = 'all';

    protected static function getPagePermission(): ?string
    {
        return 'page_project_project_budget_control';
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Project;
    }

    public static function getNavigationLabel(): string
    {
        return __('projects::filament/pages/project-budget-control.navigation.title');
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return static::$navigationIcon;
    }

    public function getTitle(): string|Htmlable
    {
        return __('projects::filament/pages/project-budget-control.title');
    }

    public function setStatusFilter(string $value): void
    {
        $this->statusFilter = $value;
    }

    /**
     * Project IDs this viewer is limited to, or null for all projects.
     * Admin and Accounting Managers see everything; accounting staff
     * follow their team scope (same rule as My Day insights).
     *
     * @return array<int, int>|null
     */
    protected function scopedProjectIds(): ?array
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return [];
        }

        if (AdminAccess::check($user) || $user->hasRole('Accounting Manager')) {
            return null;
        }

        $insightsClass = 'Webkul\\Support\\Services\\AccountingTeamInsights';

        if ($user->hasRole('Accounting') && class_exists($insightsClass)) {
            $scope = app($insightsClass)->teamScope($user);

            return $scope['scoped'] ? $scope['project_ids'] : null;
        }

        return null;
    }

    /**
     * @return array{rows: array<int, array<string, mixed>>, totals: array<string, mixed>}
     */
    public function getBudgetData(): array
    {
        $data = app(ProjectBudgetControlData::class);
        $rows = $data->rows($this->scopedProjectIds());
        $totals = $data->totals($rows);

        if (filled($this->search)) {
            $needle = mb_strtolower(trim($this->search));
            $rows = array_values(array_filter(
                $rows,
                fn (array $row): bool => str_contains(mb_strtolower($row['name']), $needle)
                    || str_contains(mb_strtolower($row['customer']), $needle),
            ));
        }

        if ($this->statusFilter !== 'all') {
            $rows = array_values(array_filter(
                $rows,
                fn (array $row): bool => $row['status'] === $this->statusFilter,
            ));
        }

        return ['rows' => $rows, 'totals' => $totals];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('exportExcel')
                ->label(__('projects::filament/pages/project-budget-control.actions.export'))
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn () => app(ProjectBudgetControlData::class)->downloadExcel($this->getBudgetData()['rows'])),
        ];
    }
}
