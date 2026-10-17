<?php

namespace Webkul\Project\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Webkul\Project\Models\Project;
use Webkul\Project\Services\CustomerProjectWeeklyReportData;
use Webkul\Security\Models\User;

class CustomerProjectWeeklyExport implements WithMultipleSheets
{
    /**
     * @param  array<string, mixed>  $report
     */
    public function __construct(
        protected array $report,
    ) {}

    public static function make(
        Project $project,
        ?User $user = null,
        string $customerNotes = '',
        mixed $startDate = null,
        mixed $endDate = null,
    ): self {
        $report = app(CustomerProjectWeeklyReportData::class)->build(
            $project,
            $user ?? auth()->user(),
            $customerNotes,
            $startDate,
            $endDate,
        );

        return new self($report);
    }

    public function sheets(): array
    {
        $cover = $this->report['cover'];
        $notes = (string) ($this->report['customer_notes'] ?? '');

        return [
            new class($cover, $notes) implements FromCollection, WithHeadings, WithTitle
            {
                public function __construct(protected array $cover, protected string $notes) {}

                public function title(): string
                {
                    return 'Cover';
                }

                public function headings(): array
                {
                    return ['Field', 'Value'];
                }

                public function collection(): Collection
                {
                    $rows = collect([
                        ['Customer', $this->cover['customer'] ?? ''],
                        ['Project', $this->cover['project'] ?? ''],
                        ['Project manager', $this->cover['manager'] ?? ''],
                        ['Report period', $this->cover['period'] ?? ''],
                        ['Generated at', $this->cover['generated_at'] ?? ''],
                    ]);

                    if ($this->notes !== '') {
                        $rows->push(['Notes for customer', $this->notes]);
                    }

                    return $rows;
                }
            },
            new class($this->report['metrics']) implements FromCollection, WithHeadings, WithTitle
            {
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
            new class($this->report['completed_rows']) implements FromCollection, WithHeadings, WithTitle
            {
                public function __construct(protected array $rows) {}

                public function title(): string
                {
                    return 'Completed in period';
                }

                public function headings(): array
                {
                    return ['Item', 'Status', 'Date'];
                }

                public function collection(): Collection
                {
                    return collect($this->rows)->map(fn (array $row): array => [
                        $row['item'],
                        $row['status'],
                        $row['date'],
                    ]);
                }
            },
            new class($this->report['open_rows']) implements FromCollection, WithHeadings, WithTitle
            {
                public function __construct(protected array $rows) {}

                public function title(): string
                {
                    return 'In progress';
                }

                public function headings(): array
                {
                    return ['Item', 'Status', 'Target date'];
                }

                public function collection(): Collection
                {
                    return collect($this->rows)->map(fn (array $row): array => [
                        $row['item'],
                        $row['status'],
                        $row['date'],
                    ]);
                }
            },
            new class($this->report['checkpoint_rows']) implements FromCollection, WithHeadings, WithTitle
            {
                public function __construct(protected array $rows) {}

                public function title(): string
                {
                    return 'Checkpoints';
                }

                public function headings(): array
                {
                    return ['Item', 'Status', 'Date'];
                }

                public function collection(): Collection
                {
                    return collect($this->rows)->map(fn (array $row): array => [
                        $row['item'],
                        $row['status'],
                        $row['date'],
                    ]);
                }
            },
        ];
    }
}
