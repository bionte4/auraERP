<?php

namespace Webkul\Evaluation\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Webkul\Evaluation\Models\Evaluation;

class EvaluationsMonitoringExport implements FromArray, WithColumnWidths, WithHeadings, WithStyles
{
    /**
     * @param  Collection<int, Evaluation>  $evaluations
     */
    public function __construct(protected Collection $evaluations) {}

    public function headings(): array
    {
        return [
            ['REKAPITULASI MONITORING EVALUASI KINERJA & TAHAPAN APPROVAL BERJENJANG (L1 -> KARYAWAN -> L2)'],
            ['Tanggal Unduh: '.now()->format('d M Y H:i')],
            [],
            [
                'No',
                'Judul Evaluasi',
                'NIK',
                'Nama Karyawan',
                'Jabatan',
                'Departemen',
                'Divisi',
                'Jenis Evaluasi',
                'Periode Evaluasi',
                'Status',
                'Pencapaian Goal (%)',
                'Total Nilai Poin D (Σ Rating)',
                'Rata-rata Poin D (Σ Rating / 10)',
                'Pemenuhan Kompetensi (%)',
                'Kriteria Terpenuhi',
                'Keputusan Rekomendasi (Poin I)',
                'Penilai L1 (Atasan Langsung)',
                'Waktu Approval L1',
                'Waktu Konfirmasi Karyawan',
                'Approver L2 (Kepala Dept)',
                'Waktu Approval L2',
                'Tahapan Approval Berjenjang',
            ],
        ];
    }

    public function array(): array
    {
        $rows = [];

        foreach ($this->evaluations->values() as $index => $record) {
            $isOutsource = $record->isOutsourceOrOuttask();
            $goal = $record->resolvedGoalAchievementPercentage();
            $workScore = $record->finalScore();
            $competency = $record->competencyAchievementPercentage();

            $rows[] = [
                $index + 1,
                $record->title ?? '—',
                $record->employee_nik ?: ($record->employee?->identification_id ?? '—'),
                $record->employee?->name ?? '—',
                $record->job_title_snapshot ?: ($record->employee?->job_title ?? '—'),
                $record->department_name ?: ($record->employee?->department?->name ?? '—'),
                $record->division_name ?: '—',
                $record->evaluation_type?->getLabel() ?? '—',
                $record->periodLabel(),
                $record->status?->getLabel() ?? '—',
                $isOutsource ? 'N/A (Outsource)' : ($goal !== null ? number_format($goal, 1).'%' : '—'),
                $workScore !== null ? $record->totalRatingScore() : 0,
                $workScore !== null ? number_format($workScore, 2) : '—',
                $competency !== null ? number_format($competency, 1).'%' : '—',
                $isOutsource ? 'N/A' : $record->criteriaMetCount().' / 3 Kriteria',
                $record->recommendation_decision?->getLabel() ?? 'Belum Ditentukan',
                $record->reviewer?->name ?? '—',
                $record->reviewer_signed_at ? $record->reviewer_signed_at->format('d/m/Y H:i') : 'Belum Disetujui',
                $record->employee_signed_at ? $record->employee_signed_at->format('d/m/Y H:i') : 'Belum Dikonfirmasi',
                $record->gmApprover?->name ?? '—',
                $record->gm_signed_at ? $record->gm_signed_at->format('d/m/Y H:i') : ($record->gm_approver_id ? 'Menunggu Approval L2' : '—'),
                $record->approvalStageLabel(),
            ];
        }

        return $rows;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 32,
            'C' => 16,
            'D' => 26,
            'E' => 24,
            'F' => 22,
            'G' => 18,
            'H' => 28,
            'I' => 26,
            'J' => 16,
            'K' => 20,
            'L' => 24,
            'M' => 26,
            'N' => 24,
            'O' => 18,
            'P' => 30,
            'Q' => 24,
            'R' => 20,
            'S' => 24,
            'T' => 24,
            'U' => 20,
            'V' => 34,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = max(4, 4 + $this->evaluations->count());

        $sheet->mergeCells('A1:V1');
        $sheet->mergeCells('A2:V2');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => [
                'bold' => true,
                'size' => 13,
            ],
        ]);

        $sheet->getStyle('A4:V4')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1E293B'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        if ($lastRow >= 4) {
            $sheet->getStyle("A4:V{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'CBD5E1'],
                    ],
                ],
            ]);
        }

        return [];
    }
}
