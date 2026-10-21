<?php

namespace Webkul\Project\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Project\Models\Project;

/**
 * Per-project budget control figures for Accounting.
 *
 * Contract value and the operational budget live on the project;
 * realization comes from posted customer invoices / vendor bills
 * linked via `accounts_account_moves.project_id`, plus timesheet
 * hours from analytic records. Projects whose documents are not
 * linked yet simply show zero realization.
 */
class ProjectBudgetControlData
{
    public const STATUS_OK = 'ok';

    public const STATUS_WARNING = 'warning';

    public const STATUS_OVER = 'over';

    public const STATUS_NO_BUDGET = 'no_budget';

    /**
     * @param  array<int, int>|null  $projectIds  null = all projects
     * @return array<int, array<string, mixed>>
     */
    public function rows(?array $projectIds = null): array
    {
        if (! Schema::hasTable('projects_projects')) {
            return [];
        }

        $projects = Project::query()
            ->withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->with(['partner' => fn ($query) => $query->select('id', 'name')])
            ->when($projectIds !== null, fn ($query) => $query->whereIn('id', $projectIds))
            ->orderBy('name')
            ->get();

        $invoiced = $this->moveAggregates('out_invoice');
        $vendorCosts = $this->moveAggregates('in_invoice');
        $hours = $this->timesheetHours();

        return $projects->map(function (Project $project) use ($invoiced, $vendorCosts, $hours): array {
            $contractValue = (float) ($project->project_value ?? 0);
            $budget = (float) ($project->operational_cost ?? 0);
            $invoicedTotal = (float) ($invoiced[$project->id]['total'] ?? 0);
            $invoicedUnpaid = (float) ($invoiced[$project->id]['residual'] ?? 0);
            $vendorCost = (float) ($vendorCosts[$project->id]['total'] ?? 0);

            $absorption = $budget > 0 ? round($vendorCost / $budget * 100, 1) : null;

            $status = match (true) {
                $budget <= 0 && $vendorCost > 0 => self::STATUS_OVER,
                $budget <= 0 => self::STATUS_NO_BUDGET,
                $absorption >= 100 => self::STATUS_OVER,
                $absorption >= 80 => self::STATUS_WARNING,
                default => self::STATUS_OK,
            };

            return [
                'id'             => (int) $project->id,
                'name'           => (string) $project->name,
                'customer'       => (string) ($project->partner?->name ?? '—'),
                'contract_value' => $contractValue,
                'invoiced'       => $invoicedTotal,
                'invoiced_pct'   => $contractValue > 0 ? round($invoicedTotal / $contractValue * 100, 1) : null,
                'unpaid'         => $invoicedUnpaid,
                'budget'         => $budget,
                'vendor_cost'    => $vendorCost,
                'absorption_pct' => $absorption,
                'hours'          => (float) ($hours[$project->id] ?? 0),
                'status'         => $status,
            ];
        })->all();
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array<string, mixed>
     */
    public function totals(array $rows): array
    {
        return [
            'projects'       => count($rows),
            'contract_value' => array_sum(array_column($rows, 'contract_value')),
            'invoiced'       => array_sum(array_column($rows, 'invoiced')),
            'unpaid'         => array_sum(array_column($rows, 'unpaid')),
            'budget'         => array_sum(array_column($rows, 'budget')),
            'vendor_cost'    => array_sum(array_column($rows, 'vendor_cost')),
            'warning'        => count(array_filter($rows, fn (array $row): bool => $row['status'] === self::STATUS_WARNING)),
            'over'           => count(array_filter($rows, fn (array $row): bool => $row['status'] === self::STATUS_OVER)),
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $rows
     */
    public function downloadExcel(array $rows): BinaryFileResponse
    {
        $headings = [
            'project', 'customer', 'contract_value', 'invoiced', 'invoiced_pct',
            'unpaid', 'operational_budget', 'vendor_cost', 'absorption_pct',
            'timesheet_hours', 'status',
        ];

        $export = new class($headings, $rows) implements FromArray, WithHeadings
        {
            /**
             * @param  array<int, string>  $headings
             * @param  array<int, array<string, mixed>>  $rows
             */
            public function __construct(
                protected array $headings,
                protected array $rows,
            ) {}

            public function headings(): array
            {
                return $this->headings;
            }

            public function array(): array
            {
                return array_map(fn (array $row): array => [
                    $row['name'],
                    $row['customer'],
                    $row['contract_value'],
                    $row['invoiced'],
                    $row['invoiced_pct'],
                    $row['unpaid'],
                    $row['budget'],
                    $row['vendor_cost'],
                    $row['absorption_pct'],
                    $row['hours'],
                    $row['status'],
                ], $this->rows);
            }
        };

        return Excel::download($export, 'project-budget-control.xlsx');
    }

    /**
     * Posted move totals per project for one move type.
     *
     * @return array<int, array{total: float, residual: float}>
     */
    protected function moveAggregates(string $moveType): array
    {
        if (! Schema::hasTable('accounts_account_moves') || ! Schema::hasColumn('accounts_account_moves', 'project_id')) {
            return [];
        }

        return DB::table('accounts_account_moves')
            ->where('move_type', $moveType)
            ->where('state', 'posted')
            ->whereNotNull('project_id')
            ->when(Schema::hasColumn('accounts_account_moves', 'deleted_at'), fn ($query) => $query->whereNull('deleted_at'))
            ->groupBy('project_id')
            ->selectRaw('project_id, sum(amount_total) as total, sum(amount_residual) as residual')
            ->get()
            ->keyBy('project_id')
            ->map(fn ($row): array => ['total' => (float) $row->total, 'residual' => (float) $row->residual])
            ->all();
    }

    /**
     * @return array<int, float>
     */
    protected function timesheetHours(): array
    {
        if (! Schema::hasTable('analytic_records')) {
            return [];
        }

        return DB::table('analytic_records')
            ->whereNotNull('project_id')
            ->when(Schema::hasColumn('analytic_records', 'deleted_at'), fn ($query) => $query->whereNull('deleted_at'))
            ->groupBy('project_id')
            ->selectRaw('project_id, sum(unit_amount) as total')
            ->pluck('total', 'project_id')
            ->map(fn ($total): float => (float) $total)
            ->all();
    }
}
