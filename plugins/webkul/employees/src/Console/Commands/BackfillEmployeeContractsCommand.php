<?php

namespace Webkul\Employee\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmployeeContract;

class BackfillEmployeeContractsCommand extends Command
{
    protected $signature = 'employees:backfill-contracts';

    protected $description = 'Create an initial "Contract #1" history record for employees that have none yet (from join/contract end dates)';

    public function handle(): int
    {
        if (! Schema::hasTable('employees_employee_contracts')) {
            $this->error('Table employees_employee_contracts does not exist. Run migrations first.');

            return self::FAILURE;
        }

        $created = 0;

        Employee::query()
            ->whereDoesntHave('contracts')
            ->where(fn ($query) => $query->whereNotNull('join_date')->orWhereNotNull('contract_end_date'))
            ->chunkById(200, function ($employees) use (&$created): void {
                foreach ($employees as $employee) {
                    $start = $employee->join_date ?? $employee->contract_end_date;

                    if (! $start) {
                        continue;
                    }

                    EmployeeContract::query()->create([
                        'employee_id'        => $employee->id,
                        'sequence'           => 1,
                        'employment_type_id' => $employee->employment_type_id,
                        'start_date'         => $start,
                        'end_date'           => $employee->contract_end_date,
                        'status'             => $employee->contract_end_date && $employee->contract_end_date->isPast()
                            ? EmployeeContract::STATUS_COMPLETED
                            : EmployeeContract::STATUS_ACTIVE,
                        'source'             => 'backfill',
                        'company_id'         => $employee->company_id,
                    ]);

                    $created++;
                }
            });

        $this->info("Backfilled {$created} initial contract record(s).");

        return self::SUCCESS;
    }
}
