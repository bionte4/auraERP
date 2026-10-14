<?php

namespace Webkul\Project\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Project\Models\Project;
use Webkul\Project\Models\ProjectStage;
use Webkul\Security\Models\User;

class ProjectBulkImportService
{
    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'name',
            'customer',
            'project_manager',
            'stage',
            'start_date',
            'end_date',
            'project_value',
            'allocated_hours',
            'visibility',
            'description',
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
                        'Implementasi ERP Fase 1',
                        'PT Telkom Pelanggan',
                        'Administrator',
                        'In Progress',
                        now()->toDateString(),
                        now()->addMonths(6)->toDateString(),
                        250000000,
                        160,
                        'internal',
                        'Implementasi modul HR, Attendance, Asset, dan Project',
                    ],
                    [
                        'Integrasi Gudang Fase 2',
                        'PT Telkom Pelanggan',
                        'Administrator',
                        'Planned',
                        now()->addMonth()->toDateString(),
                        now()->addDays(75)->toDateString(),
                        150000000,
                        120,
                        'internal',
                        'Proyek kedua untuk pelanggan yang sama',
                    ],
                ];
            }
        };

        return Excel::download($export, 'template-import-projects.xlsx');
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
        $creatorId = Auth::id();

        foreach ($rows as $rawRow) {
            $row = $isAssoc ? $this->normalizeAssocRow($rawRow) : $this->mapIndexedRow($rawRow, $headerMap);

            $name = trim((string) ($row['name'] ?? $row['nama'] ?? $row['project_name'] ?? ''));

            if ($name === '') {
                $skipped++;

                continue;
            }

            $customerId = $this->resolveCustomer($row['customer'] ?? $row['pelanggan'] ?? $row['klien'] ?? null);
            $managerId = $this->resolveUser($row['project_manager'] ?? $row['manajer_proyek'] ?? $row['manager'] ?? null);
            $stageId = $this->resolveStage($row['stage'] ?? $row['tahap'] ?? null, $companyId, $creatorId);
            $startDate = $this->parseDate($row['start_date'] ?? $row['tanggal_mulai'] ?? null);
            $endDate = $this->parseDate($row['end_date'] ?? $row['tanggal_akhir'] ?? null);
            $projectValue = $this->parseAmount($row['project_value'] ?? $row['nilai_proyek'] ?? $row['value'] ?? 0);
            $allocatedHours = isset($row['allocated_hours']) && $row['allocated_hours'] !== '' ? (float) $row['allocated_hours'] : null;
            $visibilityRaw = strtolower(trim((string) ($row['visibility'] ?? 'internal')));
            $visibility = in_array($visibilityRaw, ['private', 'internal', 'public'], true) ? $visibilityRaw : 'internal';
            $description = $this->nullIfEmpty($row['description'] ?? $row['deskripsi'] ?? null);

            $attributes = [
                'name'             => $name,
                'partner_id'       => $customerId,
                'user_id'          => $managerId,
                'stage_id'         => $stageId,
                'start_date'       => $startDate,
                'end_date'         => $endDate,
                'allocated_hours'  => $allocatedHours,
                'visibility'       => $visibility,
                'description'      => $description,
                'is_active'        => true,
                'allow_timesheets' => true,
                'allow_milestones' => true,
                'company_id'       => $companyId,
                'creator_id'       => $creatorId,
            ];

            if (Schema::hasColumn('projects_projects', 'project_value')) {
                $attributes['project_value'] = $projectValue;
            }

            $existingQuery = Project::withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);

            if ($customerId !== null) {
                $existingQuery->where('partner_id', $customerId);
            }

            $existing = $existingQuery->first();

            if ($existing) {
                $existing->forceFill(array_filter($attributes, fn ($v): bool => $v !== null))->save();
                $updated++;
            } else {
                Project::withoutGlobalScopes()->create($attributes);
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

    protected function parseAmount(mixed $value): float
    {
        if (is_int($value) || is_float($value)) {
            return (float) $value;
        }

        $str = trim((string) ($value ?? ''));

        if ($str === '') {
            return 0.0;
        }

        $cleaned = preg_replace('/[^0-9.,-]/', '', $str) ?? '';

        if (substr_count($cleaned, '.') > 1 && ! str_contains($cleaned, ',')) {
            $cleaned = str_replace('.', '', $cleaned);
        } elseif (str_contains($cleaned, '.') && str_contains($cleaned, ',')) {
            $cleaned = str_replace('.', '', $cleaned);
            $cleaned = str_replace(',', '.', $cleaned);
        }

        return is_numeric($cleaned) ? (float) $cleaned : 0.0;
    }

    protected function resolveCustomer(mixed $name): ?int
    {
        $clean = $this->nullIfEmpty($name);

        if ($clean === null || ! Schema::hasTable('partners_partners')) {
            return null;
        }

        $partner = Partner::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($clean)])
            ->first();

        if ($partner) {
            if (Schema::hasColumn('partners_partners', 'customer_rank') && (int) $partner->customer_rank < 1) {
                $partner->forceFill(['customer_rank' => 1])->save();
            }

            return (int) $partner->id;
        }

        return Project::createCustomerPartner(['name' => $clean]);
    }

    protected function resolveUser(mixed $nameOrEmail): ?int
    {
        $clean = $this->nullIfEmpty($nameOrEmail);

        if ($clean === null || ! Schema::hasTable('users')) {
            return Auth::id();
        }

        $user = User::query()
            ->where(function ($q) use ($clean): void {
                $q->whereRaw('LOWER(name) = ?', [mb_strtolower($clean)])
                    ->orWhereRaw('LOWER(email) = ?', [mb_strtolower($clean)]);
            })
            ->first();

        return $user?->id ?? Auth::id();
    }

    protected function resolveStage(mixed $stageName, ?int $companyId, ?int $creatorId): ?int
    {
        if (! Schema::hasTable('projects_project_stages')) {
            return null;
        }

        $clean = $this->nullIfEmpty($stageName);

        if ($clean !== null) {
            $stage = ProjectStage::withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($clean)])
                ->first();

            if (! $stage) {
                $stage = ProjectStage::withoutGlobalScopes()->create([
                    'name'       => $clean,
                    'company_id' => $companyId,
                    'creator_id' => $creatorId,
                ]);
            }

            return (int) $stage->id;
        }

        return ProjectStage::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->orderBy('sort')
            ->value('id');
    }
}
