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
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Filament\Widgets\Concerns\AppliesPeopleDashboardFilters;
use Webkul\Employee\Models\Employee;

class PeopleAttentionWidget extends BaseWidget
{
    use AppliesPeopleDashboardFilters;
    use HasWidgetShield, InteractsWithPageFilters;

    protected static bool $isLazy = false;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $pollingInterval = '60s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_employee_people_attention_widget';
    }

    protected function getTableHeading(): string|Htmlable|null
    {
        return __('employees::filament/widgets/people-attention.heading.title');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->attentionQuery())
            ->defaultSort('name', 'asc')
            ->defaultPaginationPageOption(5)
            ->paginationPageOptions([5, 10, 25])
            ->columns([
                TextColumn::make('name')
                    ->label(__('employees::filament/widgets/people-attention.table-columns.employee'))
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->url(fn (Employee $record): string => $this->employeeOverviewUrl($record)),
                TextColumn::make('department.name')
                    ->label(__('employees::filament/widgets/people-attention.table-columns.department'))
                    ->placeholder('—')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                TextColumn::make('parent.name')
                    ->label(__('employees::filament/widgets/people-attention.table-columns.manager'))
                    ->placeholder('—')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('issues')
                    ->label(__('employees::filament/widgets/people-attention.table-columns.issues'))
                    ->badge()
                    ->color('warning')
                    ->wrap()
                    ->state(fn (Employee $record): string => implode(' · ', $this->issueLabels($record))),
                TextColumn::make('projects_count')
                    ->label(__('employees::filament/widgets/people-attention.table-columns.projects'))
                    ->alignCenter()
                    ->sortable(),
                TextColumn::make('join_date')
                    ->label(__('employees::filament/widgets/people-attention.table-columns.join-date'))
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('issue')
                    ->label(__('employees::filament/widgets/people-attention.filters.issue.label'))
                    ->options([
                        'no_user'      => __('employees::filament/widgets/people-attention.filters.issue.no-user'),
                        'incomplete'   => __('employees::filament/widgets/people-attention.filters.issue.incomplete'),
                        'no_project'   => __('employees::filament/widgets/people-attention.filters.issue.no-project'),
                    ])
                    ->placeholder(__('employees::filament/widgets/people-attention.filters.issue.all'))
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'no_user'    => $query->whereNull('user_id'),
                            'incomplete' => $query->where(fn (Builder $inner) => $this->applyIncompleteProfile($inner)),
                            'no_project' => $this->projectsAvailable()
                                ? $query->whereDoesntHave('projects')
                                : $query,
                            default      => $query,
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
            ->emptyStateHeading(__('employees::filament/widgets/people-attention.empty.heading'))
            ->emptyStateDescription(__('employees::filament/widgets/people-attention.empty.description'))
            ->emptyStateIcon('heroicon-o-check-circle');
    }

    /**
     * Active employees missing a login, profile basics, or a project assignment.
     */
    protected function attentionQuery(): Builder
    {
        $query = $this->scopedPeopleQuery()
            ->with(['department', 'parent'])
            ->where('is_active', true)
            ->where(function (Builder $outer): void {
                $outer->whereNull('user_id')
                    ->orWhere(fn (Builder $inner) => $this->applyIncompleteProfile($inner));

                if ($this->projectsAvailable()) {
                    $outer->orWhereDoesntHave('projects');
                }
            });

        if ($this->projectsAvailable()) {
            $query->withCount('projects');
        }

        return $query;
    }

    protected function applyIncompleteProfile(Builder $query): Builder
    {
        return $query
            ->whereNull('department_id')
            ->orWhereNull('parent_id')
            ->orWhere(function (Builder $jobQuery): void {
                $jobQuery->whereNull('job_id')
                    ->where(function (Builder $titleQuery): void {
                        $titleQuery->whereNull('job_title')->orWhere('job_title', '');
                    });
            })
            ->orWhere(function (Builder $emailQuery): void {
                $emailQuery->where(function (Builder $work): void {
                    $work->whereNull('work_email')->orWhere('work_email', '');
                })->where(function (Builder $private): void {
                    $private->whereNull('private_email')->orWhere('private_email', '');
                });
            });
    }

    /**
     * @return array<int, string>
     */
    protected function issueLabels(Employee $record): array
    {
        $issues = [];

        if (! $record->user_id) {
            $issues[] = __('employees::filament/widgets/people-attention.issues.no-user');
        }

        if (! $record->department_id) {
            $issues[] = __('employees::filament/widgets/people-attention.issues.no-department');
        }

        if (! $record->job_id && blank($record->job_title)) {
            $issues[] = __('employees::filament/widgets/people-attention.issues.no-job');
        }

        if (! $record->parent_id) {
            $issues[] = __('employees::filament/widgets/people-attention.issues.no-manager');
        }

        if (blank($record->work_email) && blank($record->private_email)) {
            $issues[] = __('employees::filament/widgets/people-attention.issues.no-email');
        }

        if ($this->projectsAvailable() && (int) ($record->projects_count ?? 0) === 0) {
            $issues[] = __('employees::filament/widgets/people-attention.issues.no-project');
        }

        return $issues;
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
