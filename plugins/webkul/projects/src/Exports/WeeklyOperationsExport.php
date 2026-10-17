<?php

namespace Webkul\Project\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Webkul\Project\Services\WeeklyOperationsReportData;
use Webkul\Security\Models\User;

class WeeklyOperationsExport implements WithMultipleSheets
{
    public function __construct(
        protected ?User $user = null,
        protected ?WeeklyOperationsReportData $report = null,
    ) {
        $this->report ??= app(WeeklyOperationsReportData::class);
        $this->user ??= auth()->user();
    }

    public function sheets(): array
    {
        $data = $this->report->build($this->user);

        return [
            new class($data['metrics']) implements FromCollection, WithHeadings, WithTitle
            {
                /**
                 * @param  array<int, array{metric: string, value: int, notes: string}>  $metrics
                 */
                public function __construct(protected array $metrics) {}

                public function title(): string
                {
                    return 'Snapshot';
                }

                public function headings(): array
                {
                    return ['Metric', 'Value', 'Notes'];
                }

                public function collection(): Collection
                {
                    return collect($this->metrics)->map(fn (array $row): array => [
                        $row['metric'],
                        $row['value'],
                        $row['notes'],
                    ]);
                }
            },
            new class($data['load_rows']) implements FromCollection, WithHeadings, WithTitle
            {
                /**
                 * @param  array<int, array{name: string, projects: int, hours: float}>  $rows
                 */
                public function __construct(protected array $rows) {}

                public function title(): string
                {
                    return 'Workforce Load';
                }

                public function headings(): array
                {
                    return ['Employee', 'Projects', 'Hours'];
                }

                public function collection(): Collection
                {
                    return collect($this->rows)->map(fn (array $row): array => [
                        $row['name'],
                        $row['projects'],
                        $row['hours'],
                    ]);
                }
            },
        ];
    }

    /**
     * @return Collection<int, array{0: string, 1: int, 2: string}>
     */
    public function collection(): Collection
    {
        return collect($this->report->metrics(
            str_starts_with((string) app()->getLocale(), 'id'),
            $this->user,
        ))->map(fn (array $row): array => [
            $row['metric'],
            $row['value'],
            $row['notes'],
        ]);
    }
}
