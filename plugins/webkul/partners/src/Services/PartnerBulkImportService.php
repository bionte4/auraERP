<?php

namespace Webkul\Partner\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;

class PartnerBulkImportService
{
    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        return [
            'name',
            'role',
            'account_type',
            'email',
            'phone',
            'mobile',
            'tax_id',
            'website',
            'street1',
            'city',
            'zip',
            'reference',
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
                        'PT Nusantara Teknologi',
                        'customer',
                        'company',
                        'info@nusantara.co.id',
                        '021-5550101',
                        '08119998887',
                        '01.234.567.8-901.000',
                        'https://nusantara.co.id',
                        'Jl. Sudirman Kav. 21',
                        'Jakarta Selatan',
                        '12920',
                        'CUST-001',
                    ],
                    [
                        'PT Bhinneka Distribusi',
                        'vendor',
                        'company',
                        'sales@bhinneka.co.id',
                        '021-4440202',
                        '08128887776',
                        '02.345.678.9-012.000',
                        'https://bhinneka.co.id',
                        'Jl. Gunung Sahari No. 73',
                        'Jakarta Pusat',
                        '10610',
                        'VEND-001',
                    ],
                ];
            }
        };

        return Excel::download($export, 'template-import-customers-vendors.xlsx');
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
        $hasCustomerRank = Schema::hasColumn('partners_partners', 'customer_rank');
        $hasSupplierRank = Schema::hasColumn('partners_partners', 'supplier_rank');

        foreach ($rows as $rawRow) {
            $row = $isAssoc ? $this->normalizeAssocRow($rawRow) : $this->mapIndexedRow($rawRow, $headerMap);

            $name = trim((string) ($row['name'] ?? $row['nama'] ?? ''));

            if ($name === '') {
                $skipped++;

                continue;
            }

            $roleRaw = strtolower(trim((string) ($row['role'] ?? $row['type'] ?? $row['tipe'] ?? 'customer')));
            $acctRaw = strtolower(trim((string) ($row['account_type'] ?? $row['jenis'] ?? 'company')));
            $isIndividual = in_array($acctRaw, ['individual', 'individu', 'person'], true);

            $email = $this->nullIfEmpty($row['email'] ?? null);
            $phone = $this->nullIfEmpty($row['phone'] ?? $row['telepon'] ?? null);
            $mobile = $this->nullIfEmpty($row['mobile'] ?? $row['hp'] ?? null);
            $taxId = $this->nullIfEmpty($row['tax_id'] ?? $row['npwp'] ?? null);
            $website = $this->nullIfEmpty($row['website'] ?? null);
            $street1 = $this->nullIfEmpty($row['street1'] ?? $row['street'] ?? $row['alamat'] ?? null);
            $city = $this->nullIfEmpty($row['city'] ?? $row['kota'] ?? null);
            $zip = $this->nullIfEmpty($row['zip'] ?? $row['kode_pos'] ?? null);
            $reference = $this->nullIfEmpty($row['reference'] ?? $row['kode'] ?? null);

            $attributes = [
                'name'         => $name,
                'account_type' => $isIndividual ? AccountType::INDIVIDUAL : AccountType::COMPANY,
                'sub_type'     => $isIndividual ? 'partner' : 'company',
                'email'        => $email,
                'phone'        => $phone,
                'mobile'       => $mobile,
                'tax_id'       => $taxId,
                'website'      => $website,
                'street1'      => $street1,
                'city'         => $city,
                'zip'          => $zip,
                'reference'    => $reference,
                'company_id'   => $companyId,
                'creator_id'   => $userId,
            ];

            $existing = Partner::withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->where(function ($q) use ($name, $email): void {
                    if ($email !== null) {
                        $q->whereRaw('LOWER(email) = ?', [mb_strtolower($email)]);
                    } else {
                        $q->whereRaw('LOWER(name) = ?', [mb_strtolower($name)]);
                    }
                })
                ->first();

            if ($existing) {
                $existing->forceFill(array_filter($attributes, fn ($v): bool => $v !== null))->save();
                $partner = $existing;
                $updated++;
            } else {
                $partner = Partner::withoutGlobalScopes()->create($attributes);
                $created++;
            }

            $rankUpdates = [];

            if ($hasCustomerRank && str_contains($roleRaw, 'cust') || str_contains($roleRaw, 'pelanggan') || $roleRaw === 'both') {
                if ($hasCustomerRank) {
                    $rankUpdates['customer_rank'] = max(1, (int) ($partner->customer_rank ?? 0));
                }
            }

            if ($hasSupplierRank && (str_contains($roleRaw, 'vend') || str_contains($roleRaw, 'supp') || str_contains($roleRaw, 'pemasok') || $roleRaw === 'both')) {
                $rankUpdates['supplier_rank'] = max(1, (int) ($partner->supplier_rank ?? 0));
            }

            if ($rankUpdates !== []) {
                $partner->forceFill($rankUpdates)->save();
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
}
