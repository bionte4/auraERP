<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Webkul\Evaluation\Models\Evaluation;

class EvaluationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('A & B. Identitas Evaluasi & Data Karyawan PKWT')
                    ->schema([
                        TextEntry::make('title')
                            ->label(__('evaluations::filament/resources/evaluation.form.title')),
                        TextEntry::make('evaluation_type')
                            ->label('Jenis Evaluasi')
                            ->badge(),
                        TextEntry::make('employee.name')
                            ->label(__('evaluations::filament/resources/evaluation.form.employee'))
                            ->placeholder('—'),
                        TextEntry::make('employee_nik')
                            ->label('NIK')
                            ->state(fn (Evaluation $record): string => $record->employee_nik ?: ($record->employee?->identification_id ?? '—')),
                        TextEntry::make('job_title_snapshot')
                            ->label('Jabatan')
                            ->state(fn (Evaluation $record): string => $record->job_title_snapshot ?: ($record->employee?->job_title ?? '—')),
                        TextEntry::make('department_name')
                            ->label('Departemen / Divisi')
                            ->state(fn (Evaluation $record): string => collect([$record->division_name, $record->department_name ?: $record->employee?->department?->name])->filter()->join(' / ') ?: '—'),
                        TextEntry::make('reviewer.name')
                            ->label('Penilai (Atasan Langsung)')
                            ->placeholder('—'),
                        TextEntry::make('period')
                            ->label(__('evaluations::filament/resources/evaluation.table.columns.period'))
                            ->state(fn (Evaluation $record): string => $record->periodLabel()),
                        TextEntry::make('status')
                            ->label(__('evaluations::filament/resources/evaluation.form.status'))
                            ->badge(),
                    ])
                    ->columns(3),

                Section::make('H & I. Ringkasan Penilaian Akhir & Kriteria Kelulusan (Minimal 2 dari 3 Kriteria)')
                    ->schema([
                        TextEntry::make('goal_criterion')
                            ->label('1. Pencapaian Goal (Min. 95%)')
                            ->badge()
                            ->color(fn (Evaluation $record): string => $record->meetsGoalCriterion() ? 'success' : 'warning')
                            ->state(function (Evaluation $record): string {
                                $goal = $record->resolvedGoalAchievementPercentage();

                                return $goal === null
                                    ? 'Belum Diisi'
                                    : number_format($goal, 1).'% ('.($record->meetsGoalCriterion() ? 'MEMENUHI' : 'BELUM MEMENUHI').')';
                            }),
                        TextEntry::make('score')
                            ->label('2. Rata-rata D. Manajemen Pekerjaan (Min. 3.0)')
                            ->badge()
                            ->color(fn (Evaluation $record): string => $record->meetsWorkManagementCriterion() ? 'success' : 'warning')
                            ->state(function (Evaluation $record): string {
                                $score = $record->finalScore();

                                return $score === null
                                    ? '—'
                                    : number_format($score, 2).' / 5.00 [Total Nilai: '.$record->totalRatingScore().'] ('.($record->meetsWorkManagementCriterion() ? 'MEMENUHI' : 'BELUM MEMENUHI').')';
                            }),
                        TextEntry::make('competency_criterion')
                            ->label('3. Pemenuhan Kompetensi (Min. 90%)')
                            ->badge()
                            ->color(fn (Evaluation $record): string => $record->meetsCompetencyCriterion() ? 'success' : 'warning')
                            ->state(function (Evaluation $record): string {
                                $comp = $record->competencyAchievementPercentage();

                                return $comp === null
                                    ? 'Belum Diisi'
                                    : ($record->competencies_met_count ?? 0).'/'.($record->competencies_total_count ?? 0).' — '.number_format($comp, 1).'% ('.($record->meetsCompetencyCriterion() ? 'MEMENUHI' : 'BELUM MEMENUHI').')';
                            }),
                        TextEntry::make('eligibility_status')
                            ->label('Status Kelayakan Pengangkatan Tetap (Min. 2/3)')
                            ->badge()
                            ->color(fn (Evaluation $record): string => $record->isEligibleForPermanentAppointment() ? 'success' : 'danger')
                            ->state(fn (Evaluation $record): string => $record->criteriaMetCount().' dari 3 Kriteria Terpenuhi — '.($record->isEligibleForPermanentAppointment() ? 'LAYAK DIANGKAT TETAP' : 'BELUM MEMENUHI SYARAT PENGANGKATAN')),
                        TextEntry::make('recommendation_decision')
                            ->label('I. Kesimpulan Rekomendasi')
                            ->badge()
                            ->placeholder('Belum ditentukan'),
                        TextEntry::make('recommendation_reason')
                            ->label('Detail Pertimbangan / Tanggal Efektif')
                            ->state(function (Evaluation $record): string {
                                $parts = [];
                                if ($record->permanent_effective_date) {
                                    $parts[] = 'Efektif: '.$record->permanent_effective_date->format('d M Y');
                                }
                                if ($record->extension_months) {
                                    $parts[] = 'Durasi: '.$record->extension_months.' bulan';
                                }
                                if (filled($record->recommendation_reason)) {
                                    $parts[] = $record->recommendation_reason;
                                }

                                return ! empty($parts) ? implode(' | ', $parts) : '—';
                            }),
                    ])
                    ->columns(3),

                Section::make('F, G & J. Catatan Penilai, Pengembangan & Status Persetujuan')
                    ->schema([
                        TextEntry::make('strengths')
                            ->label('F.1 Aspek yang Menjadi Kekuatan')
                            ->placeholder('—'),
                        TextEntry::make('areas_for_improvement')
                            ->label('F.2 Aspek yang Perlu Dikembangkan')
                            ->placeholder('—'),
                        TextEntry::make('development_plan')
                            ->label('G. Usulan Pengembangan')
                            ->placeholder('—')
                            ->columnSpanFull(),
                        TextEntry::make('summary')
                            ->label(__('evaluations::filament/resources/evaluation.form.summary'))
                            ->placeholder('—'),
                        TextEntry::make('employee_comment')
                            ->label(__('evaluations::filament/resources/evaluation.form.employee-comment'))
                            ->placeholder('—'),
                        TextEntry::make('signatures_summary')
                            ->label('J. Status Persetujuan (Karyawan & Penilai)')
                            ->columnSpanFull()
                            ->state(function (Evaluation $record): string {
                                return collect([
                                    'Karyawan yang Dinilai: '.($record->employee_signed_at ? $record->employee_signed_at->format('d/m/Y H:i') : 'Belum Tanda Tangan'),
                                    'Penilai (Atasan Langsung): '.($record->reviewer_signed_at ? $record->reviewer_signed_at->format('d/m/Y H:i') : 'Belum Tanda Tangan'),
                                ])->join('  •  ');
                            }),
                    ])
                    ->columns(2),
            ]);
    }
}
