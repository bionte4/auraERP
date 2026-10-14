<?php

namespace Webkul\Employee\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use Webkul\Employee\Models\Department;
use Webkul\Employee\Models\Employee;
use Webkul\Employee\Models\EmploymentType;

class EmployeeBulkImportService
{
    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'name',
            'work_email',
            'work_phone',
            'mobile_phone',
            'job_title',
            'department',
            'employment_type',
            'join_date',
            'contract_end_date',
        ];
    }

    public function downloadTemplate(): BinaryFileResponse
    {
        $export = new class($this->headings()) implements FromArray, WithHeadings
        {
            /**
             * @param  array<int, string>  $headings
             */
            public function __construct(protected array $headings) {}

            /**
             * @return array<int, string>
             */
            public function headings(): array
            {
                return $this->headings;
            }

            /**
             * @return array<int, array<int, mixed>>
             */
            public function array(): array
            {
                return [
                    [
                        'Budi Santoso',
                        'budi.santoso@opsora.my.id',
                        '021-5551234',
                        '081234567890',
                        'Project Engineer',
                        'Engineering',
                        'Contract',
                        now()->subMonths(6)->toDateString(),
                        now()->addMonths(6)->toDateString(),
                    ],
                ];
            }
        };

        return Excel::download($export, 'template-import-employees.xlsx');
    }

    /**
     * @return array{created: int, updated: int, skipped: int}
     */
    public function importFromPath(string $filePath): array
    {
        $sheets = Excel::toArray(new class {}, $filePath);
        $rows = $sheets[0] ?? [];

        return $this->importRows($rows);
    }

    /**
     * @param  array<int, array<int|string, mixed>>  $rows
     * @return array{created: int, updated: int, skipped: int}
     */
    public function importRows(array $rows): array
    {
        if ($rows === []) {
            return ['created' => 0, 'updated' => 0, 'skipped' => 0];
        }

        $headerMap = [];
        $firstRow = $rows[0];
        $isAssoc = array_keys($firstRow) !== range(0, count($firstRow) - 1);

        if (! $isAssoc) {
            foreach ($firstRow as $idx => $colName) {
                $headerMap[$idx] = strtolower(trim((string) $colName));
            }
            array_shift($rows);
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $companyId = current_company_id() ?? Auth::user()?->default_company_id;
        $userId = Auth::id();

        foreach ($rows as $rawRow) {
            $row = $isAssoc ? $this->normalizeAssocRow($rawRow) : $this->mapIndexedRow($rawRow, $headerMap);

            $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));

            if ($name === '') {
                $skipped++;

                continue;
            }

            $email = $this->nullIfEmpty($row['work_email'] ?? $row['email'] ?? null);
            $workPhone = $this->nullIfEmpty($row['work_phone'] ?? $row['phone'] ?? null);
            $mobilePhone = $this->nullIfEmpty($row['mobile_phone'] ?? $row['mobile'] ?? null);
            $jobTitle = $this->nullIfEmpty($row['job_title'] ?? $row['jabatan'] ?? null);
            $departmentId = $this->resolveDepartment($row['department'] ?? $row['departemen'] ?? null, $companyId, $userId);
            $employmentTypeId = $this->resolveEmploymentType($row['employment_type'] ?? $row['tipe_karyawan'] ?? null, $userId);
            $joinDate = $this->parseDate($row['join_date'] ?? null);
            $contractEndDate = $this->parseDate($row['contract_end_date'] ?? null);

            $attributes = [
                'name'          => $name,
                'work_email'    => $email,
                'work_phone'    => $workPhone,
                'mobile_phone'  => $mobilePhone,
                'job_title'     => $jobTitle,
                'department_id' => $departmentId,
                'is_active'     => true,
                'company_id'    => $companyId,
                'creator_id'    => $userId,
            ];

            if (Schema::hasColumn('employees_employees', 'employment_type_id')) {
                $attributes['employment_type_id'] = $employmentTypeId;
                $attributes['join_date'] = $joinDate;
                $attributes['contract_end_date'] = $contractEndDate;
            }

            $existing = null;

            if ($email !== null) {
                $existing = Employee::withoutGlobalScopes()
                    ->whereNull('deleted_at')
                    ->whereRaw('LOWER(work_email) = ?', [mb_strtolower($email)])
                    ->first();
            }

            if ($existing) {
                $existing->forceFill(array_filter($attributes, fn ($v): bool => $v !== null))->save();
                $updated++;
            } else {
                Employee::withoutGlobalScopes()->create($attributes);
                $created++;
            }
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped];
    }

    /**
     * @param  array<int|string, mixed>  $rawRow
     * @param  array<int, string>  $headerMap
     * @return array<string, mixed>
     */
    protected function mapIndexedRow(array $rawRow, array $headerMap): array
    {
        $mapped = [];

        foreach ($rawRow as $idx => $val) {
            $key = $headerMap[$idx] ?? (string) $idx;
            $mapped[$key] = $val;
        }

        return $mapped;
    }

    /**
     * @param  array<int|string, mixed>  $rawRow
     * @return array<string, mixed>
     */
    protected function normalizeAssocRow(array $rawRow): array
    {
        $mapped = [];

        foreach ($rawRow as $k => $v) {
            $mapped[strtolower(trim((string) $k))] = $v;
        }

        return $mapped;
    }

    protected function nullIfEmpty(mixed $value): ?string
    {
        $trimmed = trim((string) ($value ?? ''));

        return $trimmed === '' ? null : $trimmed;
    }

    protected function parseDate(mixed $value): ?string
    {
        $str = $this->nullIfEmpty($value);

        if ($str === null) {
            return null;
        }

        try {
            if (is_numeric($str)) {
                return Carbon::instance(Date::excelToDateTimeObject((float) $str))->toDateString();
            }

            return Carbon::parse($str)->toDateString();
        } catch (Throwable) {
            return null;
        }
    }

    protected function resolveDepartment(mixed $name, ?int $companyId, ?int $userId): ?int
    {
        $clean = $this->nullIfEmpty($name);

        if ($clean === null || ! Schema::hasTable('employees_departments')) {
            return null;
        }

        $dept = Department::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($clean)])
            ->first();

        if (! $dept) {
            $dept = Department::withoutGlobalScopes()->create([
                'name'       => $clean,
                'company_id' => $companyId,
                'creator_id' => $userId,
            ]);
        }

        return (int) $dept->id;
    }

    protected function resolveEmploymentType(mixed $name, ?int $userId): ?int
    {
        $clean = $this->nullIfEmpty($name);

        if ($clean === null || ! Schema::hasTable('employees_employment_types')) {
            return null;
        }

        $type = EmploymentType::query()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($clean)])
            ->first();

        if (! $type) {
            $type = EmploymentType::query()->create([
                'name'       => $clean,
                'is_active'  => true,
                'creator_id' => $userId,
            ]);
        }

        return (int) $type->id;
    }
}
