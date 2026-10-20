<?php

namespace Webkul\Employee\Filament\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Throwable;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Employee\Filament\Widgets\Concerns\AppliesPeopleDashboardFilters;

class PeopleStatsWidget extends BaseWidget
{
    use AppliesPeopleDashboardFilters;
    use HasWidgetShield, InteractsWithPageFilters;

    protected ?string $pollingInterval = '60s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_employee_people_stats_widget';
    }

    protected function getHeading(): ?string
    {
        return __('employees::filament/widgets/people-stats.heading.title');
    }

    /**
     * @return array<Stat>
     */
    protected function getStats(): array
    {
        $active = (clone $this->scopedPeopleQuery())->where('is_active', true);

        $totalActive = (clone $active)->count();

        $newHires = (clone $active)
            ->where(function ($query): void {
                $query->whereDate('join_date', '>=', now()->subDays(30)->toDateString())
                    ->orWhereDate('created_at', '>=', now()->subDays(30)->toDateString());
            })
            ->count();

        $expiringContracts = (clone $active)
            ->whereNotNull('contract_end_date')
            ->whereDate('contract_end_date', '<=', now()->addDays(30)->toDateString())
            ->count();

        $withoutUser = (clone $active)->whereNull('user_id')->count();

        $withoutProject = null;

        if ($this->projectsAvailable()) {
            $withoutProject = (clone $active)->whereDoesntHave('projects')->count();
        }

        $stats = [
            Stat::make(__('employees::filament/widgets/people-stats.active'), (string) $totalActive)
                ->description(__('employees::filament/widgets/people-stats.active-help'))
                ->color('primary')
                ->url($this->employeeListUrl()),
            Stat::make(__('employees::filament/widgets/people-stats.new-hires'), (string) $newHires)
                ->description(__('employees::filament/widgets/people-stats.new-hires-help'))
                ->color('success')
                ->url($this->employeeListUrl('newly_hired')),
            Stat::make(__('employees::filament/widgets/people-stats.expiring-contracts'), (string) $expiringContracts)
                ->description(__('employees::filament/widgets/people-stats.expiring-contracts-help'))
                ->color($expiringContracts > 0 ? 'danger' : 'gray')
                ->url($this->employeeListUrl('expiring_contracts')),
            Stat::make(__('employees::filament/widgets/people-stats.without-user'), (string) $withoutUser)
                ->description(__('employees::filament/widgets/people-stats.without-user-help'))
                ->color($withoutUser > 0 ? 'warning' : 'gray')
                ->url($this->employeeListUrl()),
        ];

        if ($withoutProject !== null) {
            $stats[] = Stat::make(__('employees::filament/widgets/people-stats.without-project'), (string) $withoutProject)
                ->description(__('employees::filament/widgets/people-stats.without-project-help'))
                ->color($withoutProject > 0 ? 'warning' : 'gray')
                ->url(url('/admin/project/assign-employees'));
        }

        return $stats;
    }

    protected function employeeListUrl(?string $presetView = null): string
    {
        try {
            $url = EmployeeResource::getUrl('index');
        } catch (Throwable) {
            $url = url('/admin/employees/employees');
        }

        return $presetView === null ? $url : $url.'?activeTableView='.$presetView;
    }
}
