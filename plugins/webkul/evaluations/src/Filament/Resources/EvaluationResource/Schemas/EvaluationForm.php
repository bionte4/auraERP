<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Enums\EvaluationType;
use Webkul\Evaluation\Enums\RecommendationDecision;
use Webkul\Performance\Models\PerformancePlan;

class EvaluationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('A & B. Identitas Evaluasi & Data Karyawan PKWT')
                    ->schema([
                        TextInput::make('title')
                            ->label(__('evaluations::filament/resources/evaluation.form.title'))
                            ->placeholder('Contoh: Evaluasi Triwulan I PKWT - Okt s/d Des 2026')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        Select::make('evaluation_type')
                            ->label('Jenis / Periode Evaluasi')
                            ->options(EvaluationType::options())
                            ->default(EvaluationType::PkwtQ1->value)
                            ->required()
                            ->live()
                            ->afterStateUpdated(function (?string $state, Set $set): void {
                                if ($state === EvaluationType::PkwtFinal->value) {
                                    $set('is_final_period', true);
                                }
                            }),
                        Select::make('status')
                            ->label(__('evaluations::filament/resources/evaluation.form.status'))
                            ->options(EvaluationStatus::options())
                            ->required()
                            ->default(EvaluationStatus::Draft->value),
                        Select::make('employee_id')
                            ->label(__('evaluations::filament/resources/evaluation.form.employee'))
                            ->relationship('employee', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set): void {
                                if (! $state) {
                                    return;
                                }

                                $employee = Employee::withoutGlobalScopes()
                                    ->with(['department', 'workLocation', 'job'])
                                    ->find($state);

                                if (! $employee) {
                                    return;
                                }

                                if ($employee->parent_id) {
                                    $set('reviewer_id', $employee->parent_id);
                                }

                                $set('employee_nik', $employee->identification_id);
                                $set('join_date', $employee->join_date?->toDateString());
                                $set('department_name', $employee->department?->name);
                                $set('job_title_snapshot', $employee->job_title ?: $employee->job?->name);
                                $set('work_location_name', $employee->workLocation?->name);
                            }),
                        Select::make('reviewer_id')
                            ->label('Penilai (Atasan Langsung / Pembimbing)')
                            ->relationship('reviewer', 'name')
                            ->searchable()
                            ->preload(),
                        DatePicker::make('period_start')
                            ->label(__('evaluations::filament/resources/evaluation.form.period-start'))
                            ->required()
                            ->native(false)
                            ->default(now()->startOfQuarter()->toDateString()),
                        DatePicker::make('period_end')
                            ->label(__('evaluations::filament/resources/evaluation.form.period-end'))
                            ->required()
                            ->native(false)
                            ->default(now()->endOfQuarter()->toDateString())
                            ->minDate(fn (Get $get): mixed => $get('period_start')),
                        TextInput::make('employee_nik')
                            ->label('NIK Karyawan')
                            ->maxLength(100),
                        DatePicker::make('join_date')
                            ->label('Tanggal Masuk')
                            ->native(false),
                        TextInput::make('job_title_snapshot')
                            ->label('Jabatan')
                            ->maxLength(150),
                        TextInput::make('grade_level')
                            ->label('Jenjang Kepangkatan')
                            ->placeholder('Contoh: Staff / Band II')
                            ->maxLength(100),
                        TextInput::make('division_name')
                            ->label('Divisi')
                            ->maxLength(150),
                        TextInput::make('department_name')
                            ->label('Departemen')
                            ->maxLength(150),
                        TextInput::make('work_location_name')
                            ->label('Lokasi Kerja')
                            ->maxLength(150),
                        Toggle::make('is_final_period')
                            ->label('Periode Penilaian Akhir / Keputusan Pengangkatan PKWT')
                            ->helperText('Aktifkan jika evaluasi ini digunakan untuk keputusan pengangkatan karyawan tetap / perpanjangan / terminasi PKWT.')
                            ->live(),
                    ])
                    ->columns(2),

                Section::make('C & E. Pencapaian Goal (Goal Setting) & Penilaian Kompetensi')
                    ->schema([
                        Select::make('performance_plan_id')
                            ->label('Link Program Goal Setting (Modul Performance)')
                            ->relationship('performancePlan', 'title')
                            ->searchable()
                            ->preload()
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set): void {
                                if (! $state) {
                                    return;
                                }

                                $plan = PerformancePlan::withoutGlobalScopes()->find($state);
                                $score = $plan?->weightedScore();

                                if ($score !== null) {
                                    $set('goal_achievement_percentage', $score);
                                }
                            }),
                        TextInput::make('goal_achievement_percentage')
                            ->label('Rata-rata Pencapaian Goal (%)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(100)
                            ->step(0.1)
                            ->suffix('%')
                            ->helperText('Standar kelulusan pengangkatan tetap: minimal 95%. Otomatis terisi bila memilih Program Goal Setting.'),
                        TextInput::make('competencies_met_count')
                            ->label('Jumlah Kompetensi Memenuhi Requirement')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Bagian E & H: Jumlah kompetensi yang telah memenuhi standar jabatan.'),
                        TextInput::make('competencies_total_count')
                            ->label('Total Kompetensi yang Dipersyaratkan')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Standar kelulusan pengangkatan tetap: minimal 90% dari total kompetensi.'),
                    ])
                    ->columns(2),

                Section::make('F & G. Catatan Penilai, Usulan Pengembangan & Tanggapan Karyawan')
                    ->schema([
                        Textarea::make('strengths')
                            ->label('F.1 Aspek yang Menjadi Kekuatan')
                            ->rows(3),
                        Textarea::make('areas_for_improvement')
                            ->label('F.2 Aspek yang Perlu Dikembangkan')
                            ->rows(3),
                        Textarea::make('development_plan')
                            ->label('G. Usulan Pengembangan (Training / Coaching / Penugasan)')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('summary')
                            ->label(__('evaluations::filament/resources/evaluation.form.summary'))
                            ->helperText(__('evaluations::filament/resources/evaluation.form.summary-helper'))
                            ->rows(2)
                            ->columnSpanFull(),
                        Textarea::make('employee_comment')
                            ->label(__('evaluations::filament/resources/evaluation.form.employee-comment'))
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('H, I & J. Kesimpulan Keputusan PKWT & Kolom Persetujuan')
                    ->schema([
                        Select::make('recommendation_decision')
                            ->label('I. Kesimpulan Rekomendasi')
                            ->options(RecommendationDecision::options())
                            ->live()
                            ->helperText('Kriteria pengangkatan tetap (minimal 2 dari 3): Goal >= 95%, Rata-rata Manajemen Pekerjaan >= 3.0, Kompetensi >= 90%.'),
                        DatePicker::make('permanent_effective_date')
                            ->label('Efektif Diangkat Tetap Per Tanggal')
                            ->native(false)
                            ->visible(fn (Get $get): bool => $get('recommendation_decision') === RecommendationDecision::AppointPermanent->value),
                        TextInput::make('extension_months')
                            ->label('Durasi Perpanjangan PKWT (Bulan)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(24)
                            ->suffix('bulan')
                            ->visible(fn (Get $get): bool => $get('recommendation_decision') === RecommendationDecision::ExtendPkwt->value),
                        Textarea::make('recommendation_reason')
                            ->label('Pertimbangan Perpanjangan / Alasan Terminasi / Catatan Keputusan')
                            ->rows(2)
                            ->columnSpanFull(),
                        Select::make('gm_approver_id')
                            ->label('General Manager (Penyetuju)')
                            ->relationship('gmApprover', 'name')
                            ->searchable()
                            ->preload(),
                        Select::make('director_approver_id')
                            ->label('Direktur Bidang (Khusus Periode Akhir / Pengangkatan)')
                            ->relationship('directorApprover', 'name')
                            ->searchable()
                            ->preload(),
                        DateTimePicker::make('employee_signed_at')
                            ->label('Tanggal Tanda Tangan Karyawan')
                            ->native(false),
                        DateTimePicker::make('reviewer_signed_at')
                            ->label('Tanggal Tanda Tangan Penilai (Atasan Langsung)')
                            ->native(false),
                        DateTimePicker::make('gm_signed_at')
                            ->label('Tanggal Tanda Tangan General Manager')
                            ->native(false),
                        DateTimePicker::make('director_signed_at')
                            ->label('Tanggal Tanda Tangan Direktur Bidang')
                            ->native(false),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }
}
