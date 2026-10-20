<?php

namespace Webkul\Employee\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Throwable;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Filament\Widgets\Concerns\AppliesPeopleDashboardFilters;
use Webkul\Employee\Models\Employee;

class ExpiringContractsWidget extends BaseWidget
{
    use AppliesPeopleDashboardFilters;
    use HasWidgetShield, InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '60s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_employee_expiring_contracts_widget';
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('employees::filament/widgets/expiring-contracts.heading.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->expiringContractsQuery())
            ->defaultSort('contract_end_date', 'asc')
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25])
            ->columns([
                TextColumn::make('name')
                    ->label(__('employees::filament/widgets/expiring-contracts.table-columns.employee'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->url(fn (Employee $record): string => $this->employeeOverviewUrl($record)),
                TextColumn::make('department.name')
                    ->label(__('employees::filament/widgets/expiring-contracts.table-columns.department'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('job_title_display')
                    ->label(__('employees::filament/widgets/expiring-contracts.table-columns.job'))
                    ->state(fn (Employee $record): string => $record->job?->name ?? (filled($record->job_title) ? $record->job_title : '—'))
                    ->wrap(),
                TextColumn::make('employmentType.name')
                    ->label(__('employees::filament/widgets/expiring-contracts.table-columns.employment-type'))
                    ->placeholder('—')
                    ->wrap(),
                TextColumn::make('parent.name')
                    ->label(__('employees::filament/widgets/expiring-contracts.table-columns.manager'))
                    ->placeholder('—')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('contract_end_date')
                    ->label(__('employees::filament/widgets/expiring-contracts.table-columns.contract-end'))
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('contract_status')
                    ->label(__('employees::filament/widgets/expiring-contracts.table-columns.status'))
                    ->badge()
                    ->state(fn (Employee $record): string => $this->contractStatusLabel($record))
                    ->color(fn (Employee $record): string => $this->contractStatusColor($record)),
                TextColumn::make('projects_count')
                    ->label(__('employees::filament/widgets/expiring-contracts.table-columns.projects'))
                    ->alignCenter()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('contract_window')
                    ->label(__('employees::filament/widgets/expiring-contracts.filters.window.label'))
                    ->options([
                        'expired'   => __('employees::filament/widgets/expiring-contracts.filters.window.expired'),
                        'within_7'  => __('employees::filament/widgets/expiring-contracts.filters.window.within-7'),
                        'within_30' => __('employees::filament/widgets/expiring-contracts.filters.window.within-30'),
                        '31_90'     => __('employees::filament/widgets/expiring-contracts.filters.window.31-90'),
                    ])
                    ->placeholder(__('employees::filament/widgets/expiring-contracts.filters.window.all'))
                    ->query(function (Builder $query, array $data): Builder {
                        $today = now()->startOfDay()->toDateString();

                        return match ($data['value'] ?? null) {
                            'expired'   => $query->whereDate('contract_end_date', '<', $today),
                            'within_7'  => $query->whereDate('contract_end_date', '>=', $today)
                                ->whereDate('contract_end_date', '<=', now()->addDays(7)->toDateString()),
                            'within_30' => $query->whereDate('contract_end_date', '>=', $today)
                                ->whereDate('contract_end_date', '<=', now()->addDays(30)->toDateString()),
                            '31_90'     => $query->whereDate('contract_end_date', '>', now()->addDays(30)->toDateString())
                                ->whereDate('contract_end_date', '<=', now()->addDays(90)->toDateString()),
                            default     => $query,
                        };
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn (Employee $record): string => $this->employeeViewUrl($record)),
                EditAction::make()
                    ->url(fn (Employee $record): string => $this->employeeEditUrl($record)),
            ])
            ->recordUrl(fn (Employee $record): string => $this->employeeOverviewUrl($record))
            ->emptyStateHeading(__('employees::filament/widgets/expiring-contracts.empty.heading'))
            ->emptyStateDescription(__('employees::filament/widgets/expiring-contracts.empty.description'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    /**
     * Active employees whose contract already ended or ends within ~3 months.
     */
    protected function expiringContractsQuery(): Builder
    {
        $cutoffDate = max(
            now()->addDays(90)->toDateString(),
            now()->addMonths(3)->toDateString(),
        );

        $query = $this->scopedPeopleQuery()
            ->with(['department', 'job', 'employmentType', 'parent'])
            ->where('is_active', true)
            ->whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', '<=', $cutoffDate);

        if ($this->projectsAvailable()) {
            $query->withCount('projects');
        }

        return $query;
    }

    protected function contractStatusLabel(Employee $record): string
    {
        $days = $record->daysUntilContractEnds();

        if ($days === null) {
            return '—';
        }

        if ($days < 0) {
            return __('employees::filament/widgets/expiring-contracts.status.overdue', ['days' => abs($days)]);
        }

        if ($days === 0) {
            return __('employees::filament/widgets/expiring-contracts.status.ends-today');
        }

        return __('employees::filament/widgets/expiring-contracts.status.days-left', ['days' => $days]);
    }

    protected function contractStatusColor(Employee $record): string
    {
        $days = $record->daysUntilContractEnds();

        return match (true) {
            $days === null => 'gray',
            $days <= 7     => 'danger',
            $days <= 30    => 'warning',
            default        => 'info',
        };
    }

    protected function employeeOverviewUrl(Employee $employee): string
    {
        try {
            return EmployeeResource::getUrl('overview', ['record' => $employee]);
        } catch (Throwable) {
            return url('/admin/employees/employees/'.$employee->getRouteKey().'/overview');
        }
    }

    protected function employeeViewUrl(Employee $employee): string
    {
        try {
            return EmployeeResource::getUrl('view', ['record' => $employee]);
        } catch (Throwable) {
            return url('/admin/employees/employees/'.$employee->getRouteKey());
        }
    }

    protected function employeeEditUrl(Employee $employee): string
    {
        try {
            return EmployeeResource::getUrl('edit', ['record' => $employee]);
        } catch (Throwable) {
            return url('/admin/employees/employees/'.$employee->getRouteKey().'/edit');
        }
    }
}
