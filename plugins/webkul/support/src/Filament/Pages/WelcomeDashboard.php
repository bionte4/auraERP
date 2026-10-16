<?php

namespace Webkul\Support\Filament\Pages;

use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Filament\Resources\EmployeeResource;
use Webkul\Project\Filament\Resources\ProjectResource;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Models\Scopes\CompanyScope;
use Webkul\Support\Services\CompanyContext;

class WelcomeDashboard extends BaseDashboard
{
    protected string $view = 'filament.pages.welcome-dashboard';

    protected static string $routePath = 'welcome';

    protected static ?int $navigationSort = -10;

    public static function canAccess(): bool
    {
        $user = auth()->user();

        if (! $user || ! method_exists($user, 'hasRole')) {
            return false;
        }

        $adminRole = config('filament-shield.panel_user.name', 'Admin');

        // Welcome overview is an Admin executive surface only — not HRD / OM / Staff / Kasir.
        return $user->hasRole($adminRole);
    }

    public static function getNavigationLabel(): string
    {
        return 'Welcome Overview';
    }

    public function getTitle(): string|Htmlable
    {
        return 'Welcome';
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Dashboard;
    }

    public static function getNavigationIcon(): string|BackedEnum|Htmlable|null
    {
        return 'heroicon-o-squares-2x2';
    }

    /**
     * @return array<string, mixed>
     */
    public function getDashboardData(): array
    {
        $totalProjects = $this->safeCount('projects_projects', ['is_active' => true]);
        $totalEmployees = $this->safeCount('employees_employees', ['is_active' => true]);

        $totalTasks = $this->safeCount('projects_tasks');
        $completedTasks = $this->safeCountWhere('projects_tasks', fn (Builder $q) => $q->whereIn('state', ['1_done', 'done', '04_done']));

        // Welcome "Total Revenue" / OpEx are project fields (project_value + operational_cost).
        $totalRevenue = $this->safeSum('projects_projects', 'project_value', ['is_active' => true]);
        $operationalCost = $this->operationalCostTotal();
        $grossMargin = $totalRevenue > 0
            ? round((($totalRevenue - $operationalCost) / $totalRevenue) * 100, 1)
            : null;

        $totalEvaluations = $this->safeCount('evaluations');
        $approvedEvaluations = $this->safeCountWhere(
            'evaluations',
            fn (Builder $q) => $q->whereIn('status', ['approved', 'gm_approved', 'hr_approved', 'completed'])
        );

        $projectUrl = $this->projectIndexUrl();
        $employeeUrl = $this->employeeIndexUrl();
        $goalsUrl = $this->firstExistingUrl([
            '/admin/performance-evaluation-dashboard',
            '/admin/welcome',
        ]);
        $placementUrl = $this->firstExistingUrl([
            '/admin/employees/configurations/work-locations',
            '/admin/employees/employees',
            '/admin/welcome',
        ]);

        if ($totalTasks > 0) {
            $completionRate = round(($completedTasks / max($totalTasks, 1)) * 100, 2);
            $rateLabel = 'Completion Rate';
            $rateHref = $projectUrl;
        } elseif ($totalEvaluations > 0) {
            $completionRate = round(($approvedEvaluations / max($totalEvaluations, 1)) * 100, 2);
            $rateLabel = 'Approval Rate';
            $rateHref = $goalsUrl;
        } else {
            $completionRate = 0.0;
            $rateLabel = 'Completion Rate';
            $rateHref = $projectUrl;
        }

        $projectDelta = $this->periodDeltaPercent('projects_projects', 'week', ['is_active' => true]);
        $employeeDelta = $this->periodDeltaPercent('employees_employees', 'month', ['is_active' => true]);
        $revenueDelta = $this->revenueDeltaPercent();
        $rateDelta = $this->periodDeltaPercent('projects_tasks', 'week');

        // Monthly series derived from live project / employee / task activity when available.
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthlyBar = $this->monthlyCounts('projects_projects', ['is_active' => true]);
        $monthlyActive = $this->monthlyCounts('employees_employees', ['is_active' => true]);
        $monthlyCurve = $this->monthlyTaskCompletionRates();
        $monthlyRevenueTrend = $this->monthlyRevenueSeries();

        $placements = $this->getIndonesiaPlacementPresence();

        $evalCompleted = $totalEvaluations;
        $evalTarget = max($totalEmployees, $totalEvaluations, 1);
        $evalProgress = min(100, (int) round(($evalCompleted / max($evalTarget, 1)) * 100));

        $attendanceCount = $this->safeCount('attendances_attendances');
        $attendanceTarget = max($totalEmployees * 22, $attendanceCount, 1);
        $attendanceProgress = min(100, (int) round(($attendanceCount / max($attendanceTarget, 1)) * 100));

        $projectProgress = $totalTasks > 0
            ? min(100, (int) round(($completedTasks / max($totalTasks, 1)) * 100))
            : 0;

        $goals = [
            [
                'goal'      => 'Evaluasi triwulan',
                'completed' => number_format($evalCompleted).' / '.number_format($evalTarget),
                'target'    => number_format($evalTarget).' Employees',
                'progress'  => $evalProgress,
            ],
            [
                'goal'      => 'Absensi operasional',
                'completed' => number_format($attendanceCount),
                'target'    => number_format($attendanceTarget).' Check-ins',
                'progress'  => $attendanceProgress,
            ],
            [
                'goal'      => 'Kesiapan proyek',
                'completed' => $projectProgress.'%',
                'target'    => ($totalProjects > 0 ? $totalProjects.' Active Projects' : 'No active projects'),
                'progress'  => $projectProgress,
            ],
        ];

        return [
            'kpis' => [
                [
                    'title'     => 'Total Project',
                    'value'     => $this->formatCompactNumber($totalProjects),
                    'raw_value' => $totalProjects,
                    'change'    => $projectDelta['label'],
                    'trend'     => $projectDelta['trend'],
                    'period'    => $projectDelta['period'],
                    'color'     => 'blue',
                    'icon'      => 'briefcase',
                    'href'      => $this->projectIndexUrl(),
                ],
                [
                    'title'     => 'Total Revenue',
                    'value'     => 'Rp '.$this->formatCompactNumber($totalRevenue),
                    'raw_value' => $totalRevenue,
                    'detail'    => $this->revenueCostDetail($operationalCost, $grossMargin),
                    'change'    => $revenueDelta['label'],
                    'trend'     => $revenueDelta['trend'],
                    'period'    => $revenueDelta['period'],
                    'color'     => 'green',
                    'icon'      => 'banknotes',
                    'href'      => $this->projectIndexUrl(),
                ],
                [
                    'title'     => 'Total Employee',
                    'value'     => $this->formatCompactNumber($totalEmployees),
                    'raw_value' => $totalEmployees,
                    'change'    => $employeeDelta['label'],
                    'trend'     => $employeeDelta['trend'],
                    'period'    => $employeeDelta['period'],
                    'color'     => 'amber',
                    'icon'      => 'users',
                    'href'      => $employeeUrl,
                ],
                [
                    'title'     => $rateLabel,
                    'value'     => number_format((float) $completionRate, 2).'%',
                    'raw_value' => $completionRate,
                    'change'    => $rateDelta['label'],
                    'trend'     => $rateDelta['trend'],
                    'period'    => $rateDelta['period'],
                    'color'     => 'teal',
                    'icon'      => 'chart-pie',
                    'href'      => $rateHref,
                ],
            ],
            'chart' => [
                'months'       => $months,
                'bar'          => $monthlyBar,
                'active'       => $monthlyActive,
                'curve'        => $monthlyCurve,
                'revenueTrend' => $monthlyRevenueTrend,
            ],
            'goals' => $goals,
            'sites' => $placements,
            'links' => [
                'project'  => $projectUrl,
                'employee' => $employeeUrl,
                'goals'    => $goalsUrl,
                'sites'    => $placementUrl,
            ],
        ];
    }

    protected function formatCompactNumber(float|int $number): string
    {
        if ($number >= 1_000_000_000) {
            return number_format($number / 1_000_000_000, 2).'B';
        }

        if ($number >= 1_000_000) {
            return number_format($number / 1_000_000, 2).'M';
        }

        if ($number >= 1_000) {
            return number_format($number / 1_000, 2).'k';
        }

        return number_format($number);
    }

    /**
     * @param  array<string, mixed>  $where
     */
    protected function safeCount(string $table, array $where = []): int
    {
        try {
            if (! Schema::hasTable($table)) {
                return 0;
            }

            $query = $this->scopedQuery($table);

            foreach ($where as $column => $value) {
                if (Schema::hasColumn($table, $column)) {
                    $query->where($column, $value);
                }
            }

            return (int) $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    protected function safeCountWhere(string $table, callable $callback): int
    {
        try {
            if (! Schema::hasTable($table)) {
                return 0;
            }

            $query = $this->scopedQuery($table);
            $callback($query);

            return (int) $query->count();
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * @param  array<string, mixed>  $where
     */
    protected function safeSum(string $table, string $column, array $where = []): float
    {
        try {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) {
                return 0.0;
            }

            $query = $this->scopedQuery($table);

            foreach ($where as $col => $val) {
                if (Schema::hasColumn($table, $col)) {
                    $query->where($col, $val);
                }
            }

            return (float) ($query->sum($column) ?? 0.0);
        } catch (Throwable) {
            return 0.0;
        }
    }

    /**
     * Apply soft-delete and company filters used by Welcome KPI queries.
     *
     * Mirrors {@see CompanyScope}: include rows whose
     * company_id is in the active company set OR is null, and skip company filtering
     * entirely when the user bypasses company scope (e.g. Admin).
     */
    protected function scopedQuery(string $table): Builder
    {
        $query = DB::table($table);

        if (Schema::hasColumn($table, 'deleted_at')) {
            $query->whereNull('deleted_at');
        }

        if (! Schema::hasColumn($table, 'company_id')) {
            return $query;
        }

        try {
            $context = app(CompanyContext::class);

            if (! $context->internalUser() || $context->bypassed()) {
                return $query;
            }

            $companyIds = $this->activeCompanyIds();

            if ($companyIds === []) {
                return $query;
            }

            $query->where(function (Builder $companyQuery) use ($companyIds): void {
                $companyQuery
                    ->whereIn('company_id', $companyIds)
                    ->orWhereNull('company_id');
            });
        } catch (Throwable) {
            // Keep unfiltered count if company context is unavailable.
        }

        return $query;
    }

    /**
     * @return array<int, int>
     */
    protected function activeCompanyIds(): array
    {
        try {
            return array_values(array_filter(
                array_map('intval', app(CompanyContext::class)->activeIds())
            ));
        } catch (Throwable) {
            return [];
        }
    }

    /**
     * @param  array<string, mixed>  $where
     * @return array{label: string, trend: string, period: string}
     */
    protected function periodDeltaPercent(string $table, string $period, array $where = []): array
    {
        $periodLabel = $period === 'week' ? 'Since last week' : 'Since last month';

        try {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'created_at')) {
                return ['label' => '', 'trend' => 'flat', 'period' => $periodLabel];
            }

            $currentStart = $period === 'week' ? now()->startOfWeek() : now()->startOfMonth();
            $previousStart = $period === 'week' ? now()->subWeek()->startOfWeek() : now()->subMonth()->startOfMonth();
            $previousEnd = $currentStart->copy()->subSecond();

            $currentQuery = $this->scopedQuery($table)->where('created_at', '>=', $currentStart);
            $previousQuery = $this->scopedQuery($table)
                ->whereBetween('created_at', [$previousStart, $previousEnd]);

            foreach ($where as $column => $value) {
                if (Schema::hasColumn($table, $column)) {
                    $currentQuery->where($column, $value);
                    $previousQuery->where($column, $value);
                }
            }

            $current = (int) $currentQuery->count();
            $previous = (int) $previousQuery->count();

            if ($previous === 0 && $current === 0) {
                return ['label' => '0%', 'trend' => 'flat', 'period' => $periodLabel];
            }

            if ($previous === 0) {
                return ['label' => '100%', 'trend' => 'up', 'period' => $periodLabel];
            }

            $delta = round((($current - $previous) / max($previous, 1)) * 100, 2);

            return [
                'label'  => number_format(abs($delta), 2).'%',
                'trend'  => $delta >= 0 ? 'up' : 'down',
                'period' => $periodLabel,
            ];
        } catch (Throwable) {
            return ['label' => '', 'trend' => 'flat', 'period' => $periodLabel];
        }
    }

    /**
     * @return array{label: string, trend: string, period: string}
     */
    protected function revenueDeltaPercent(): array
    {
        $periodLabel = 'Since last month';

        try {
            $current = $this->revenueBetween(now()->startOfMonth(), now());
            $previous = $this->revenueBetween(now()->subMonth()->startOfMonth(), now()->startOfMonth()->subSecond());

            if ($previous <= 0.0 && $current <= 0.0) {
                return ['label' => '0%', 'trend' => 'flat', 'period' => $periodLabel];
            }

            if ($previous <= 0.0) {
                return ['label' => '100%', 'trend' => 'up', 'period' => $periodLabel];
            }

            $delta = round((($current - $previous) / max($previous, 1)) * 100, 2);

            return [
                'label'  => number_format(abs($delta), 2).'%',
                'trend'  => $delta >= 0 ? 'up' : 'down',
                'period' => $periodLabel,
            ];
        } catch (Throwable) {
            return ['label' => '', 'trend' => 'flat', 'period' => $periodLabel];
        }
    }

    protected function revenueBetween($from, $to): float
    {
        $table = 'projects_projects';
        $column = 'project_value';

        if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column) || ! Schema::hasColumn($table, 'created_at')) {
            return 0.0;
        }

        $query = $this->scopedQuery($table)->whereBetween('created_at', [$from, $to]);

        if (Schema::hasColumn($table, 'is_active')) {
            $query->where('is_active', true);
        }

        return (float) ($query->sum($column) ?? 0.0);
    }

    /**
     * Sum of operational_cost on active projects (same source pattern as Total Revenue).
     */
    protected function operationalCostTotal(): float
    {
        return $this->safeSum('projects_projects', 'operational_cost', ['is_active' => true]);
    }

    protected function revenueCostDetail(float $operationalCost, ?float $grossMargin): string
    {
        $indonesian = str_starts_with((string) app()->getLocale(), 'id');
        $opex = 'Rp '.$this->formatCompactNumber($operationalCost);

        if ($grossMargin === null) {
            return ($indonesian ? 'OpEx: ' : 'OpEx: ').$opex;
        }

        return ($indonesian ? 'OpEx: ' : 'OpEx: ').$opex
            .' · '.($indonesian ? 'Margin: ' : 'Margin: ').number_format($grossMargin, 1).'%';
    }

    protected function projectIndexUrl(): string
    {
        try {
            if (class_exists(ProjectResource::class)) {
                return ProjectResource::getUrl('index', panel: 'admin');
            }
        } catch (Throwable) {
            // Fall through when the Projects plugin routes are unavailable.
        }

        return $this->firstExistingUrl(['/admin/project', '/admin/team-board', '/admin/welcome']);
    }

    protected function employeeIndexUrl(): string
    {
        try {
            if (class_exists(EmployeeResource::class)) {
                return EmployeeResource::getUrl('index', panel: 'admin');
            }
        } catch (Throwable) {
            //
        }

        return $this->firstExistingUrl(['/admin/employees/employees', '/admin/welcome']);
    }

    /**
     * @param  array<int, string>  $paths
     */
    protected function firstExistingUrl(array $paths): string
    {
        foreach ($paths as $path) {
            $absolute = str_starts_with($path, 'http') ? $path : url($path);
            $relative = parse_url($absolute, PHP_URL_PATH) ?: $path;

            try {
                $route = app('router')->getRoutes()->match(
                    Request::create((string) $relative, 'GET')
                );

                if ($route !== null) {
                    return $absolute;
                }
            } catch (Throwable) {
                continue;
            }
        }

        return url('/admin/welcome');
    }

    /**
     * @param  array<string, mixed>  $where
     * @return array<int, int>
     */
    protected function monthlyCounts(string $table, array $where = []): array
    {
        $series = array_fill(0, 12, 0);

        try {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'created_at')) {
                return $series;
            }

            $year = now()->year;

            foreach (range(1, 12) as $month) {
                $query = $this->scopedQuery($table)
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month);

                foreach ($where as $column => $value) {
                    if (Schema::hasColumn($table, $column)) {
                        $query->where($column, $value);
                    }
                }

                $series[$month - 1] = (int) $query->count();
            }
        } catch (Throwable) {
            // Keep zero series when the dialect or table is unavailable.
        }

        return $series;
    }

    /**
     * @return array<int, float>
     */
    protected function monthlyTaskCompletionRates(): array
    {
        $series = array_fill(0, 12, 0.0);

        try {
            if (! Schema::hasTable('projects_tasks') || ! Schema::hasColumn('projects_tasks', 'created_at')) {
                return $series;
            }

            $year = now()->year;

            foreach (range(1, 12) as $month) {
                $total = (int) $this->scopedQuery('projects_tasks')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->count();

                $completed = (int) $this->scopedQuery('projects_tasks')
                    ->whereYear('created_at', $year)
                    ->whereMonth('created_at', $month)
                    ->whereIn('state', ['1_done', 'done', '04_done'])
                    ->count();

                $series[$month - 1] = $total > 0 ? round(($completed / $total) * 100, 1) : 0.0;
            }
        } catch (Throwable) {
            //
        }

        return $series;
    }

    /**
     * @return array<int, float>
     */
    protected function monthlyRevenueSeries(): array
    {
        $series = array_fill(0, 12, 0.0);
        $year = now()->year;

        foreach (range(1, 12) as $month) {
            $from = now()->setDate($year, $month, 1)->startOfMonth();
            $to = $from->copy()->endOfMonth();
            $series[$month - 1] = $this->revenueBetween($from, $to);
        }

        $max = max($series) ?: 1.0;

        return array_map(fn (float $value): float => round(($value / $max) * 100, 1), $series);
    }

    /**
     * Welcome Indonesia map shows employee placement by Work Location (not attendance geofence).
     * Markers are clustered by city so regional density stays readable.
     *
     * @return array{islands: array<int, string>, markers: array<int, array<string, mixed>>, total: int, cities: int}
     */
    protected function getIndonesiaPlacementPresence(): array
    {
        $islands = [];

        foreach ($this->indonesiaIslands() as $outline) {
            $points = [];

            foreach ($outline as [$lat, $lng]) {
                [$x, $y] = $this->projectIndonesia($lat, $lng);
                $points[] = $x.','.$y;
            }

            $islands[] = implode(' ', $points);
        }

        $markers = [];
        $total = 0;
        $palette = ['#3b6ef6', '#38a169', '#f6ad55', '#31b8c6', '#7c3aed', '#e11d48'];

        try {
            if (
                ! Schema::hasTable('employees_work_locations')
                || ! Schema::hasTable('employees_employees')
                || ! Schema::hasColumn('employees_work_locations', 'latitude')
                || ! Schema::hasColumn('employees_work_locations', 'longitude')
            ) {
                return ['islands' => $islands, 'markers' => [], 'total' => 0, 'cities' => 0];
            }

            $placedByLocation = [];

            $employeeRows = $this->scopedQuery('employees_employees')
                ->when(Schema::hasColumn('employees_employees', 'is_active'), fn ($query) => $query->where('is_active', true))
                ->whereNotNull('work_location_id')
                ->orderBy('name')
                ->get(['work_location_id', 'name']);

            foreach ($employeeRows as $row) {
                $placedByLocation[(int) $row->work_location_id][] = (string) $row->name;
            }

            $locations = $this->scopedQuery('employees_work_locations')
                ->when(Schema::hasColumn('employees_work_locations', 'is_active'), fn ($query) => $query->where('is_active', true))
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->orderBy('name')
                ->get(['id', 'name', 'city', 'latitude', 'longitude']);

            /** @var array<string, array{name: string, names: array<int, string>, latSum: float, lngSum: float, points: int, sites: int}> $clusters */
            $clusters = [];

            foreach ($locations as $location) {
                $names = $placedByLocation[(int) $location->id] ?? [];
                $label = filled($location->city ?? null)
                    ? (string) $location->city
                    : (string) $location->name;
                $key = mb_strtolower(trim($label));

                if ($key === '') {
                    continue;
                }

                if (! isset($clusters[$key])) {
                    $clusters[$key] = [
                        'name'   => $label,
                        'names'  => [],
                        'latSum' => 0.0,
                        'lngSum' => 0.0,
                        'points' => 0,
                        'sites'  => 0,
                    ];
                }

                $clusters[$key]['names'] = array_values(array_unique([
                    ...$clusters[$key]['names'],
                    ...$names,
                ]));
                $clusters[$key]['latSum'] += (float) $location->latitude;
                $clusters[$key]['lngSum'] += (float) $location->longitude;
                $clusters[$key]['points']++;
                $clusters[$key]['sites']++;
            }

            foreach (array_values($clusters) as $index => $cluster) {
                $count = count($cluster['names']);
                $total += $count;
                $lat = $cluster['latSum'] / max($cluster['points'], 1);
                $lng = $cluster['lngSum'] / max($cluster['points'], 1);
                [$x, $y] = $this->projectIndonesia($lat, $lng);

                $markers[] = [
                    'name'  => $cluster['name'],
                    'count' => $count,
                    'sites' => $cluster['sites'],
                    'names' => array_slice($cluster['names'], 0, 5),
                    'extra' => max(0, $count - 5),
                    'x'     => $x,
                    'y'     => $y,
                    'color' => $palette[$index % count($palette)],
                ];
            }

            usort($markers, fn (array $a, array $b): int => $b['count'] <=> $a['count']);
        } catch (Throwable) {
            $markers = [];
            $total = 0;
        }

        return [
            'islands' => $islands,
            'markers' => $markers,
            'total'   => $total,
            'cities'  => count($markers),
        ];
    }

    /**
     * @return array{0: float, 1: float}
     */
    protected function projectIndonesia(float $latitude, float $longitude): array
    {
        $minLongitude = 94.0;
        $maxLongitude = 141.5;
        $minLatitude = -11.2;
        $maxLatitude = 6.3;

        $x = (($longitude - $minLongitude) / ($maxLongitude - $minLongitude)) * 860 + 20;
        $y = (($maxLatitude - $latitude) / ($maxLatitude - $minLatitude)) * 340 + 18;

        return [round($x, 1), round($y, 1)];
    }

    /**
     * Simplified island outlines as latitude, longitude pairs.
     *
     * @return array<int, array<int, array{0: float, 1: float}>>
     */
    protected function indonesiaIslands(): array
    {
        return [
            [[5.6, 95.3], [5.4, 97.4], [3.6, 98.8], [1.8, 100.4], [-0.6, 102.2], [-2.6, 104.4], [-5.2, 105.6], [-5.9, 104.4], [-4.2, 102.6], [-2.2, 100.6], [-0.2, 98.8], [2.2, 97.2], [4.2, 95.8]],
            [[-5.9, 105.7], [-6.1, 106.9], [-6.3, 108.2], [-6.7, 109.6], [-6.9, 110.8], [-7.3, 112.4], [-7.8, 113.6], [-8.6, 114.4], [-8.2, 113.2], [-7.7, 111.4], [-7.5, 109.4], [-7.2, 107.4], [-6.6, 106.0]],
            [[4.4, 117.6], [3.4, 118.0], [1.6, 118.4], [-0.2, 117.6], [-2.2, 116.2], [-3.6, 114.2], [-3.9, 111.8], [-2.4, 109.6], [-0.2, 108.9], [1.8, 109.2], [3.4, 111.6], [4.4, 114.8], [4.6, 116.4]],
            [[1.7, 125.0], [1.2, 125.4], [0.4, 123.8], [-1.2, 122.6], [-2.6, 121.6], [-3.8, 122.6], [-5.4, 122.6], [-5.6, 120.0], [-4.2, 119.4], [-2.6, 119.3], [-0.8, 119.6], [0.6, 120.2], [1.6, 121.6], [1.8, 123.6]],
            [[-0.6, 130.8], [-1.2, 134.2], [-2.2, 138.2], [-2.6, 141.0], [-5.2, 141.0], [-8.2, 140.6], [-9.0, 138.2], [-7.6, 137.2], [-5.4, 138.0], [-3.8, 135.4], [-2.6, 132.2], [-1.6, 130.8]],
            [[-8.2, 114.6], [-8.4, 115.6], [-8.5, 116.6], [-8.6, 118.2], [-8.5, 119.8], [-8.6, 121.2], [-8.8, 122.4], [-9.6, 124.0], [-10.4, 123.6], [-9.2, 122.2], [-8.8, 120.2], [-8.6, 117.4], [-8.5, 115.2]],
            [[2.2, 128.2], [1.4, 127.6], [0.4, 127.8], [-0.6, 127.4], [-1.6, 128.2], [-2.4, 128.4], [-3.2, 128.8], [-2.2, 129.6], [-0.8, 129.4], [0.8, 129.2], [1.8, 128.8]],
        ];
    }
}
