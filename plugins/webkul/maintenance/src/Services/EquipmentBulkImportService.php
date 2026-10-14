<?php

namespace Webkul\Maintenance\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Maintenance\Models\Equipment;
use Webkul\Maintenance\Models\EquipmentCategory;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;

class EquipmentBulkImportService
{
    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'name',
            'serial_no',
            'model',
            'category',
            'vendor',
            'employee',
            'project',
            'customer',
            'location',
            'cost',
            'effective_date',
            'warranty_date',
            'note',
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
                        'MacBook Pro M4 #A01',
                        'SN-MBP-001',
                        'MacBook Pro 14"',
                        'IT & Laptop',
                        'PT Bhinneka Vendor',
                        'Andi Engineer',
                        'Instalasi Server',
                        'PT Klien Nusantara',
                        'Kantor Pusat Lt. 2',
                        28500000,
                        now()->toDateString(),
                        now()->addYear()->toDateString(),
                        'Unit inventaris engineer',
                    ],
                ];
            }
        };

        return Excel::download($export, 'template-import-equipment-assets.xlsx');
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
                $normalized = strtolower(trim((string) $colName));
                $headerMap[$idx] = $normalized;
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

            $serialNo = $this->nullIfEmpty($row['serial_no'] ?? $row['nomor_seri'] ?? null);
            $model = $this->nullIfEmpty($row['model'] ?? null);
            $location = $this->nullIfEmpty($row['location'] ?? $row['lokasi'] ?? null);
            $note = $this->nullIfEmpty($row['note'] ?? $row['deskripsi'] ?? null);
            $cost = isset($row['cost']) && $row['cost'] !== '' ? (float) $row['cost'] : 0.0;
            $effectiveDate = $this->parseDate($row['effective_date'] ?? null) ?? now()->toDateString();
            $warrantyDate = $this->parseDate($row['warranty_date'] ?? null);

            $categoryId = $this->resolveCategory($row['category'] ?? $row['kategori'] ?? null, $companyId, $userId);
            $vendorId = $this->resolvePartner($row['vendor'] ?? $row['pemasok'] ?? null, 'vendor');
            $employee = $this->resolveEmployee($row['employee'] ?? $row['karyawan'] ?? null);
            $project = $this->resolveProject($row['project'] ?? $row['proyek'] ?? null);
            $customerId = $this->resolvePartner($row['customer'] ?? $row['klien'] ?? null, 'customer')
                ?? $project?->partner_id;

            $attributes = [
                'name'           => $name,
                'serial_no'      => $serialNo,
                'model'          => $model,
                'category_id'    => $categoryId,
                'partner_id'     => $vendorId,
                'location'       => $location,
                'cost'           => $cost,
                'effective_date' => $effectiveDate,
                'warranty_date'  => $warrantyDate,
                'note'           => $note,
                'company_id'     => $companyId,
                'creator_id'     => $userId,
            ];

            if ($employee) {
                $attributes['owner_user_id'] = $employee->user_id;
            }

            if (Schema::hasColumn('maintenance_equipments', 'employee_id')) {
                $attributes['employee_id'] = $employee?->id;
                $attributes['project_id'] = $project?->id;
                $attributes['customer_id'] = $customerId;
            }

            $existing = null;

            if ($serialNo !== null) {
                $existing = Equipment::withoutGlobalScopes()
                    ->whereNull('deleted_at')
                    ->where('serial_no', $serialNo)
                    ->first();
            }

            if ($existing) {
                $existing->forceFill(array_filter($attributes, fn ($v): bool => $v !== null))->save();
                $updated++;
            } else {
                Equipment::withoutGlobalScopes()->create($attributes);
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

    protected function resolveCategory(mixed $name, ?int $companyId, ?int $userId): ?int
    {
        $clean = $this->nullIfEmpty($name);

        if ($clean === null || ! Schema::hasTable('maintenance_equipment_categories')) {
            return null;
        }

        $category = EquipmentCategory::withoutGlobalScopes()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($clean)])
            ->first();

        if (! $category) {
            $category = EquipmentCategory::withoutGlobalScopes()->create([
                'name'       => $clean,
                'company_id' => $companyId,
                'creator_id' => $userId,
            ]);
        }

        return (int) $category->id;
    }

    protected function resolvePartner(mixed $name, string $role): ?int
    {
        $clean = $this->nullIfEmpty($name);

        if ($clean === null || ! Schema::hasTable('partners_partners')) {
            return null;
        }

        $partner = Partner::withoutGlobalScopes()
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($clean)])
            ->first();

        if ($partner) {
            return (int) $partner->id;
        }

        return Equipment::createExternalPartner(['name' => $clean], $role);
    }

    protected function resolveEmployee(mixed $name): ?Employee
    {
        $clean = $this->nullIfEmpty($name);

        if ($clean === null || ! Schema::hasTable('employees_employees')) {
            return null;
        }

        return Employee::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->where(function ($q) use ($clean): void {
                $q->whereRaw('LOWER(name) = ?', [mb_strtolower($clean)])
                    ->orWhereRaw('LOWER(work_email) = ?', [mb_strtolower($clean)]);
            })
            ->first();
    }

    protected function resolveProject(mixed $name): ?Project
    {
        $clean = $this->nullIfEmpty($name);

        if ($clean === null || ! Schema::hasTable('projects_projects')) {
            return null;
        }

        return Project::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($clean)])
            ->first();
    }
}
