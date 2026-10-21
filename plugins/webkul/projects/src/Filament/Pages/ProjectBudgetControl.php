<?php

namespace Webkul\Project\Filament\Pages;

use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Webkul\Project\Models\Project;
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

    public ?int $editingBudgetProjectId = null;

    public string $budgetAmount = '';

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
     * Only Admins and Accounting Managers may set a project's
     * operational budget. Accounting staff monitor realization; the
     * budget itself is a management decision (segregation of duties),
     * and general project editing stays with Admin / OM.
     */
    public function canEditBudgets(): bool
    {
        $user = auth()->user();

        if (! $user instanceof User) {
            return false;
        }

        return AdminAccess::check($user)
            || (method_exists($user, 'hasRole') && $user->hasRole('Accounting Manager'));
    }

    public function startEditBudget(int $projectId): void
    {
        if (! $this->canEditBudgets() || ! $this->projectInScope($projectId)) {
            return;
        }

        $project = $this->findProject($projectId);

        if (! $project) {
            return;
        }

        $this->editingBudgetProjectId = $project->id;
        $this->budgetAmount = rtrim(rtrim(number_format((float) ($project->operational_cost ?? 0), 2, '.', ''), '0'), '.');
        $this->budgetAmount = $this->budgetAmount === '' || $this->budgetAmount === '-0' ? '0' : $this->budgetAmount;
    }

    public function cancelEditBudget(): void
    {
        $this->editingBudgetProjectId = null;
        $this->budgetAmount = '';
    }

    public function saveBudget(): void
    {
        if (! $this->canEditBudgets() || $this->editingBudgetProjectId === null) {
            return;
        }

        $this->validate([
            'budgetAmount' => ['required', 'numeric', 'min:0'],
        ]);

        if (! $this->projectInScope($this->editingBudgetProjectId)) {
            return;
        }

        $project = $this->findProject($this->editingBudgetProjectId);

        if (! $project) {
            $this->cancelEditBudget();

            return;
        }

        // Only the operational budget is touched — contract value,
        // dates, manager and other project fields stay with Admin/OM.
        $project->forceFill(['operational_cost' => (float) $this->budgetAmount])->save();

        $this->cancelEditBudget();

        Notification::make()
            ->success()
            ->title(__('projects::filament/pages/project-budget-control.notifications.budget-saved'))
            ->send();
    }

    protected function projectInScope(int $projectId): bool
    {
        $scoped = $this->scopedProjectIds();

        return $scoped === null || in_array($projectId, $scoped, true);
    }

    protected function findProject(int $projectId): ?Project
    {
        return Project::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->find($projectId);
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
