<?php

namespace Webkul\Evaluation\Services;

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
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Enums\EvaluationType;
use Webkul\Evaluation\Enums\RecommendationDecision;
use Webkul\Evaluation\Models\Evaluation;

class EvaluationBulkImportService
{
    /**
     * @return array<int, string>
     */
    public function headings(): array
    {
        $scoreHeadings = [];

        for ($i = 1; $i <= 10; $i++) {
            $scoreHeadings[] = 'score_'.$i;
        }

        for ($i = 1; $i <= 10; $i++) {
            $scoreHeadings[] = 'comment_'.$i;
        }

        return [
            'employee_nik',
            'employee_name',
            'title',
            'evaluation_type',
            'period_start',
            'period_end',
            'goal_achievement_percentage',
            'competencies_met_count',
            'competencies_total_count',
            'recommendation_decision',
            'extension_months',
            'permanent_effective_date',
            'summary',
            'strengths',
            'areas_for_improvement',
            'development_plan',
            'recommendation_reason',
            ...$scoreHeadings,
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
                $start = now()->startOfQuarter()->toDateString();
                $end = now()->endOfQuarter()->toDateString();

                return [
                    [
                        'EMP001',
                        'Budi Santoso',
                        'Evaluasi Triwulan I PKWT',
                        'pkwt_q1',
                        $start,
                        $end,
                        96,
                        9,
                        10,
                        'extend_pkwt',
                        6,
                        '',
                        'Kinerja sesuai ekspektasi pada periode ini.',
                        'Disiplin dan komunikasi baik.',
                        'Perlu peningkatan inisiatif.',
                        'Coaching bulanan dengan atasan langsung.',
                        '',
                        4, 4, 3, 4, 4, 4, 3, 4, 3, 4,
                        '', '', '', '', '', '', '', '', '', '',
                    ],
                ];
            }
        };

        return Excel::download($export, 'template-import-evaluations.xlsx');
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
                $headerMap[$idx] = $this->normalizeHeader((string) $colName);
            }
            array_shift($rows);
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $companyId = current_company_id() ?? Auth::user()?->default_company_id;

        foreach ($rows as $rawRow) {
            $row = $isAssoc ? $this->normalizeAssocRow($rawRow) : $this->mapIndexedRow($rawRow, $headerMap);

            if ($this->isEmptyRow($row)) {
                $skipped++;

                continue;
            }

            $employee = $this->resolveEmployee($row);

            if (! $employee) {
                $skipped++;

                continue;
            }

            $evaluationType = $this->resolveEvaluationType($row['evaluation_type'] ?? $row['tipe_evaluasi'] ?? null);

            if (! $evaluationType) {
                $skipped++;

                continue;
            }

            $periodStart = $this->parseDate($row['period_start'] ?? $row['periode_mulai'] ?? null);
            $periodEnd = $this->parseDate($row['period_end'] ?? $row['periode_selesai'] ?? null);

            if ($periodStart === null || $periodEnd === null) {
                $skipped++;

                continue;
            }

            $existing = Evaluation::withoutGlobalScopes()
                ->where('employee_id', $employee->id)
                ->where('evaluation_type', $evaluationType->value)
                ->whereDate('period_start', $periodStart)
                ->whereDate('period_end', $periodEnd)
                ->first();

            if ($existing && $existing->status === EvaluationStatus::Completed) {
                $skipped++;

                continue;
            }

            $recommendation = $this->resolveRecommendation($row['recommendation_decision'] ?? $row['rekomendasi'] ?? null);
            $title = $this->nullIfEmpty($row['title'] ?? $row['judul'] ?? null)
                ?? ($evaluationType->getLabel().' — '.$employee->name);

            $attributes = [
                'company_id'                  => $employee->company_id ?? $companyId,
                'employee_id'                 => $employee->id,
                'title'                       => $title,
                'evaluation_type'             => $evaluationType,
                'is_final_period'             => $evaluationType === EvaluationType::PkwtFinal,
                'period_start'                => $periodStart,
                'period_end'                  => $periodEnd,
                'goal_achievement_percentage' => $this->parseFloat($row['goal_achievement_percentage'] ?? $row['persentase_goal'] ?? null),
                'competencies_met_count'      => $this->parseInt($row['competencies_met_count'] ?? $row['kompetensi_terpenuhi'] ?? null),
                'competencies_total_count'    => $this->parseInt($row['competencies_total_count'] ?? $row['kompetensi_total'] ?? null),
                'recommendation_decision'     => $recommendation,
                'extension_months'            => $this->parseInt($row['extension_months'] ?? $row['bulan_perpanjangan'] ?? null),
                'permanent_effective_date'    => $this->parseDate($row['permanent_effective_date'] ?? $row['tanggal_efektif'] ?? null),
                'summary'                     => $this->nullIfEmpty($row['summary'] ?? $row['ringkasan'] ?? null),
                'strengths'                   => $this->nullIfEmpty($row['strengths'] ?? $row['kelebihan'] ?? null),
                'areas_for_improvement'       => $this->nullIfEmpty($row['areas_for_improvement'] ?? $row['area_perbaikan'] ?? null),
                'development_plan'            => $this->nullIfEmpty($row['development_plan'] ?? $row['rencana_pengembangan'] ?? null),
                'recommendation_reason'       => $this->nullIfEmpty($row['recommendation_reason'] ?? $row['alasan_rekomendasi'] ?? null),
                'status'                      => EvaluationStatus::InReview,
            ];

            if ($evaluationType === EvaluationType::OutsourceOuttask || Evaluation::isEmployeeOutsourceOrOuttask($employee)) {
                $attributes['evaluation_type'] = EvaluationType::OutsourceOuttask;
                $attributes['goal_achievement_percentage'] = null;
                $attributes['performance_plan_id'] = null;
            }

            if ($existing) {
                $existing->fill($attributes);
                $existing->save();
                $evaluation = $existing;
                $updated++;
            } else {
                $evaluation = Evaluation::withoutGlobalScopes()->create($attributes);
                $created++;
            }

            $this->applyScores($evaluation, $row);

            if ($evaluation->goal_achievement_percentage === null) {
                $evaluation->syncGoalFromPerformancePlan();
            }
        }

        return ['created' => $created, 'updated' => $updated, 'skipped' => $skipped];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    protected function applyScores(Evaluation $evaluation, array $row): void
    {
        $evaluation->seedDefaultPkwtCriteria();

        $ratings = $evaluation->ratings()->orderBy('sort')->orderBy('id')->get();

        foreach ($ratings as $rating) {
            $sort = (int) $rating->sort;
            $score = $this->parseScore($row['score_'.$sort] ?? $row['nilai_'.$sort] ?? null);

            if ($score === null) {
                continue;
            }

            $rating->score = $score;

            $comment = $this->nullIfEmpty($row['comment_'.$sort] ?? $row['komentar_'.$sort] ?? null);

            if ($comment !== null) {
                $rating->comment = $comment;
            }

            $rating->save();
        }

        $evaluation->unsetRelation('ratings');
    }

    /**
     * @param  array<string, mixed>  $row
     */
    protected function resolveEmployee(array $row): ?Employee
    {
        if (! Schema::hasTable('employees_employees')) {
            return null;
        }

        $nik = $this->nullIfEmpty($row['employee_nik'] ?? $row['nik'] ?? null);
        $name = $this->nullIfEmpty($row['employee_name'] ?? $row['nama_karyawan'] ?? $row['name'] ?? null);

        if ($nik !== null) {
            $byNik = Employee::withoutGlobalScopes()
                ->whereNull('deleted_at')
                ->whereRaw('LOWER(identification_id) = ?', [mb_strtolower($nik)])
                ->first();

            if ($byNik) {
                return $byNik;
            }
        }

        if ($name === null) {
            return null;
        }

        return Employee::withoutGlobalScopes()
            ->whereNull('deleted_at')
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();
    }

    protected function resolveEvaluationType(mixed $value): ?EvaluationType
    {
        $clean = $this->nullIfEmpty($value);

        if ($clean === null) {
            return null;
        }

        $normalized = mb_strtolower($clean);
        $normalized = str_replace(['_', '-'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        foreach (EvaluationType::cases() as $case) {
            if (mb_strtolower($case->value) === str_replace(' ', '_', $normalized)) {
                return $case;
            }

            if (mb_strtolower($case->getLabel()) === $normalized) {
                return $case;
            }
        }

        return match ($normalized) {
            'pkwt q1', 'triwulan 1', 'triwulan i', 'q1', 'tw1'       => EvaluationType::PkwtQ1,
            'pkwt q2', 'triwulan 2', 'triwulan ii', 'q2', 'tw2'      => EvaluationType::PkwtQ2,
            'pkwt q3', 'triwulan 3', 'triwulan iii', 'q3', 'tw3'     => EvaluationType::PkwtQ3,
            'pkwt final', 'evaluasi akhir', 'akhir pkwt', 'final'    => EvaluationType::PkwtFinal,
            'outsource', 'outtask', 'outsource outtask', 'alih daya' => EvaluationType::OutsourceOuttask,
            'regular', 'berkala', 'evaluasi reguler'                 => EvaluationType::Regular,
            default                                                  => EvaluationType::tryFrom(str_replace(' ', '_', $normalized)),
        };
    }

    protected function resolveRecommendation(mixed $value): ?RecommendationDecision
    {
        $clean = $this->nullIfEmpty($value);

        if ($clean === null) {
            return null;
        }

        $normalized = mb_strtolower($clean);
        $normalized = str_replace(['_', '-'], ' ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        foreach (RecommendationDecision::cases() as $case) {
            if (mb_strtolower($case->value) === str_replace(' ', '_', $normalized)) {
                return $case;
            }

            if (mb_strtolower($case->getLabel()) === $normalized) {
                return $case;
            }
        }

        return match (true) {
            str_contains($normalized, 'tetap') || str_contains($normalized, 'appoint')                    => RecommendationDecision::AppointPermanent,
            str_contains($normalized, 'perpanjang') && str_contains($normalized, 'out')                   => RecommendationDecision::ExtendOuttaskOutsource,
            str_contains($normalized, 'perpanjang') || str_contains($normalized, 'extend pkwt')           => RecommendationDecision::ExtendPkwt,
            str_contains($normalized, 'alih') || str_contains($normalized, 'transfer')                    => RecommendationDecision::TransferOuttaskOutsource,
            str_contains($normalized, 'tidak diperpanjang') && str_contains($normalized, 'out')           => RecommendationDecision::TerminateOuttaskOutsource,
            str_contains($normalized, 'terminasi') || str_contains($normalized, 'tidak direkomendasikan') => RecommendationDecision::TerminatePkwt,
            default                                                                                       => RecommendationDecision::tryFrom(str_replace(' ', '_', $normalized)),
        };
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
            $mapped[$this->normalizeHeader((string) $k)] = $v;
        }

        return $mapped;
    }

    protected function normalizeHeader(string $header): string
    {
        return strtolower(trim($header));
    }

    /**
     * @param  array<string, mixed>  $row
     */
    protected function isEmptyRow(array $row): bool
    {
        foreach ($row as $value) {
            if ($this->nullIfEmpty($value) !== null) {
                return false;
            }
        }

        return true;
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

    protected function parseFloat(mixed $value): ?float
    {
        $str = $this->nullIfEmpty($value);

        if ($str === null) {
            return null;
        }

        $str = str_replace(',', '.', $str);

        if (! is_numeric($str)) {
            return null;
        }

        return round((float) $str, 1);
    }

    protected function parseInt(mixed $value): ?int
    {
        $str = $this->nullIfEmpty($value);

        if ($str === null || ! is_numeric($str)) {
            return null;
        }

        return (int) $str;
    }

    protected function parseScore(mixed $value): ?int
    {
        $score = $this->parseInt($value);

        if ($score === null || $score < 1 || $score > 5) {
            return null;
        }

        return $score;
    }
}
