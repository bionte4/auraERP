<?php

namespace Webkul\Project\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Throwable;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Project\Filament\Widgets\Concerns\AppliesDashboardFilters;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\ProjectBudgetControlData;

/**
 * Operational budget vs actual opex per project for operational
 * roles (Operation Manager / Team Leader), scoped to the projects
 * they manage or lead. Deliberately shows NO contract value and no
 * invoicing: contract values are financial data reserved for Admin
 * and the budget-control team (see
 * OperationalScope::canViewProjectValue()).
 */
class OperationalBudgetWidget extends BaseWidget
{
    use AppliesDashboardFilters;
    use HasWidgetShield, InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '30s';

    /** @var array<int, array<string, mixed>>|null */
    protected ?array $budgetRows = null;

    protected static function getPagePermission(): ?string
    {
        return 'widget_project_operational_budget_widget';
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('projects::filament/widgets/operational-budget.heading.title');
    }

    public function table(Table $table): Table
    {
        $money = fn (float $value): string => 'Rp '.number_format($value, 0, ',', '.');

        return $table
            ->query($this->projectsQuery())
            ->queryStringIdentifier('opexBudget')
            ->defaultSort('name', 'asc')
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25])
            ->columns([
                TextColumn::make('name')
                    ->label(__('projects::filament/widgets/operational-budget.table-columns.project'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->url(fn (Project $record): string => $this->projectViewUrl($record)),
                TextColumn::make('partner.name')
                    ->label(__('projects::filament/widgets/operational-budget.table-columns.customer'))
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('operational_cost')
                    ->label(__('projects::filament/widgets/operational-budget.table-columns.budget'))
                    ->state(fn (Project $record): string => $money((float) ($record->operational_cost ?? 0))),
                TextColumn::make('vendor_cost')
                    ->label(__('projects::filament/widgets/operational-budget.table-columns.vendor-cost'))
                    ->state(fn (Project $record): string => $money($this->budgetRow($record)['vendor_cost'])),
                TextColumn::make('remaining')
                    ->label(__('projects::filament/widgets/operational-budget.table-columns.remaining'))
                    ->state(fn (Project $record): string => $this->budgetRow($record)['budget'] > 0
                        ? $money($this->budgetRow($record)['remaining'])
                        : '—')
                    ->color(fn (Project $record): string => $this->budgetRow($record)['remaining'] < 0 ? 'danger' : 'gray')
                    ->weight(fn (Project $record): FontWeight => $this->budgetRow($record)['remaining'] < 0 ? FontWeight::Bold : FontWeight::Normal),
                TextColumn::make('absorption')
                    ->label(__('projects::filament/widgets/operational-budget.table-columns.absorption'))
                    ->badge()
                    ->state(fn (Project $record): string => $this->statusLabel($this->budgetRow($record)['status']))
                    ->color(fn (Project $record): string => match ($this->budgetRow($record)['status']) {
                        ProjectBudgetControlData::STATUS_OVER    => 'danger',
                        ProjectBudgetControlData::STATUS_WARNING => 'warning',
                        ProjectBudgetControlData::STATUS_OK      => 'success',
                        default                                  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state, Project $record): string => $this->budgetRow($record)['absorption_pct'] !== null
                        ? $this->budgetRow($record)['absorption_pct'].'% · '.$state
                        : $state),
            ])
            ->emptyStateHeading(__('projects::filament/widgets/operational-budget.empty.heading'))
            ->emptyStateDescription(__('projects::filament/widgets/operational-budget.empty.description'));
    }

    protected function statusLabel(string $status): string
    {
        return __('projects::filament/pages/project-budget-control.status.'.$status);
    }

    /**
     * @return array{vendor_cost: float, budget: float, remaining: float, absorption_pct: float|null, status: string}
     */
    protected function budgetRow(Project $project): array
    {
        $row = $this->budgetRows()[$project->id] ?? null;

        return [
            'vendor_cost'    => (float) ($row['vendor_cost'] ?? 0),
            'budget'         => (float) ($row['budget'] ?? $project->operational_cost ?? 0),
            'remaining'      => (float) ($row['remaining'] ?? 0),
            'absorption_pct' => $row['absorption_pct'] ?? null,
            'status'         => (string) ($row['status'] ?? ProjectBudgetControlData::STATUS_NO_BUDGET),
        ];
    }

    /**
     * Budget-control rows (budget, vendor actuals, absorption) keyed
     * by project id, limited to the viewer's operational scope.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function budgetRows(): array
    {
        if ($this->budgetRows === null) {
            $rows = app(ProjectBudgetControlData::class)->rows($this->operationalProjectIds());

            $this->budgetRows = collect($rows)->keyBy('id')->all();
        }

        return $this->budgetRows;
    }

    protected function projectsQuery(): Builder
    {
        $query = Project::query()
            ->with(['partner'])
            ->where('is_active', true);

        $query = $this->applyOperationalProjectScope($query, 'id');

        $selectedProjects = $this->selectedFilterIds('selectedProjects');

        if ($selectedProjects !== []) {
            $query->whereIn('id', $selectedProjects);
        }

        return $query;
    }

    protected function projectViewUrl(Project $project): string
    {
        try {
            return ProjectResource::getUrl('view', ['record' => $project]);
        } catch (Throwable) {
            return url('/admin/project/projects/'.$project->getRouteKey());
        }
    }
}
