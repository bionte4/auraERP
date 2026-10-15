<?php

namespace Webkul\Evaluation\Filament\Resources\EvaluationResource\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
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
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Performance\Models\PerformancePlan;

class EvaluationForm
{
    public static function isOutsourceOrOuttaskState(Get $get): bool
    {
        $evalType = $get('evaluation_type');

        if ($evalType instanceof EvaluationType) {
            $evalType = $evalType->value;
        }

        if ($evalType === EvaluationType::OutsourceOuttask->value) {
            return true;
        }

        $decision = $get('recommendation_decision');

        if ($decision instanceof RecommendationDecision) {
            $decision = $decision->value;
        }

        if (in_array($decision, [
            RecommendationDecision::ExtendOuttaskOutsource->value,
            RecommendationDecision::TerminateOuttaskOutsource->value,
        ], true)) {
            return true;
        }

        $employeeId = $get('employee_id');

        if ($employeeId) {
            $employee = Employee::withoutGlobalScopes()->with('employmentType')->find($employeeId);

            if (Evaluation::isEmployeeOutsourceOrOuttask($employee)) {
                return true;
            }
        }

        return false;
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('A & B. Identitas Evaluasi & Data Karyawan')
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

                                if ($state === EvaluationType::OutsourceOuttask->value) {
                                    $set('performance_plan_id', null);
                                    $set('goal_achievement_percentage', null);
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
                                    ->with(['department', 'workLocation', 'job', 'employmentType'])
                                    ->find($state);

                                if (! $employee) {
                                    return;
                                }

                                if (Evaluation::isEmployeeOutsourceOrOuttask($employee)) {
                                    $set('evaluation_type', EvaluationType::OutsourceOuttask->value);
                                    $set('performance_plan_id', null);
                                    $set('goal_achievement_percentage', null);
                                }

                                $levelOne = $employee->resolveLevelOneApproverEmployee();
                                $levelTwo = $employee->resolveLevelTwoApproverEmployee();

                                if ($levelOne) {
                                    $set('reviewer_id', $levelOne->id);
                                } elseif ($employee->parent_id) {
                                    $set('reviewer_id', $employee->parent_id);
                                }

                                if ($levelTwo) {
                                    $set('gm_approver_id', $levelTwo->id);
                                }

                                $set('employee_nik', $employee->identification_id);
                                $set('join_date', $employee->join_date?->toDateString());
                                $set('department_name', $employee->department?->name);
                                $set('job_title_snapshot', $employee->job_title ?: $employee->job?->name);
                                $set('work_location_name', $employee->workLocation?->name);
                            }),
                        Select::make('reviewer_id')
                            ->label('Approver Level 1: Penilai (Atasan Langsung / Pembimbing)')
                            ->relationship('reviewer', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Otomatis terisi dari Atasan Langsung karyawan di struktur organisasi.'),
                        Select::make('gm_approver_id')
                            ->label('Approver Level 2: Atasan Penilai / Kepala Departemen (Berjenjang)')
                            ->relationship('gmApprover', 'name')
                            ->searchable()
                            ->preload()
                            ->helperText('Otomatis terisi dari Atasan Level 2 / Manager Departemen di struktur organisasi.'),
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
                            ->label('Periode Penilaian Akhir / Keputusan Kontrak')
                            ->helperText('Aktifkan jika evaluasi ini digunakan untuk keputusan akhir (pengangkatan tetap / perpanjangan / outtask-outsource / terminasi).')
                            ->live(),
                    ])
                    ->columns(2),

                Section::make('C. Pencapaian Goal Karyawan')
                    ->visible(fn (Get $get): bool => ! static::isOutsourceOrOuttaskState($get))
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
                    ])
                    ->columns(2),

                Section::make('D. Manajemen Pekerjaan (Poin 1 – 10)')
                    ->description('Berikan nilai Rating skala 1 s/d 5 beserta Komentar justifikasi untuk setiap aspek penilaian manajemen pekerjaan di bawah ini.')
                    ->schema([
                        Repeater::make('ratings')
                            ->relationship('ratings')
                            ->label('Aspek Penilaian Manajemen Pekerjaan')
                            ->default(fn (): array => Evaluation::defaultPkwtCriteria())
                            ->orderColumn('sort')
                            ->itemLabel(fn (array $state): ?string => isset($state['sort'], $state['title'])
                                ? "{$state['sort']}. {$state['title']}"
                                : ($state['title'] ?? 'Aspek Penilaian'))
                            ->schema([
                                Hidden::make('sort')
                                    ->default(1),
                                Hidden::make('weight')
                                    ->default(10),
                                TextInput::make('title')
                                    ->label('Aspek Penilaian')
                                    ->required()
                                    ->maxLength(255)
                                    ->columnSpan(1),
                                Select::make('score')
                                    ->label('Rating (Skala 1 – 5)')
                                    ->options([
                                        5 => '5 — Secara konsisten melampaui standar yang diharapkan',
                                        4 => '4 — Secara signifikan melebihi standar yang diharapkan',
                                        3 => '3 — Memenuhi standar yang diharapkan',
                                        2 => '2 — Kurang dari standar yang diharapkan',
                                        1 => '1 — Sama sekali tidak memenuhi standar yang diharapkan',
                                    ])
                                    ->native(false)
                                    ->placeholder('Pilih Rating 1 - 5')
                                    ->columnSpan(1),
                                Textarea::make('description')
                                    ->label('Acuan / Deskripsi Aspek')
                                    ->rows(2)
                                    ->disabled()
                                    ->dehydrated()
                                    ->columnSpanFull(),
                                Textarea::make('comment')
                                    ->label('Komentar / Justifikasi Penilai')
                                    ->placeholder('Tuliskan komentar sebagai justifikasi atas rating yang diberikan pada aspek ini...')
                                    ->rows(2)
                                    ->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->addActionLabel('Tambah Aspek Penilaian Lainnya')
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                Section::make('E. Penilaian Kompetensi Karyawan')
                    ->schema([
                        TextInput::make('competencies_met_count')
                            ->label('Jumlah Kompetensi yang Memenuhi Requirement')
                            ->numeric()
                            ->minValue(0)
                            ->helperText('Jumlah kompetensi yang telah memenuhi standar requirement jabatan.'),
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

                Section::make('H, I & J. Kesimpulan Keputusan & Approval Berjenjang')
                    ->schema([
                        Select::make('recommendation_decision')
                            ->label('I. Kesimpulan Rekomendasi')
                            ->options(RecommendationDecision::options())
                            ->live()
                            ->helperText('Kriteria pengangkatan tetap (minimal 2 dari 3): Pencapaian Goal >= 95%, Rata-rata Manajemen Pekerjaan (Kolom D) >= 3.0, Kompetensi >= 90%.'),
                        DatePicker::make('permanent_effective_date')
                            ->label('Efektif Per Tanggal')
                            ->native(false)
                            ->visible(fn (Get $get): bool => in_array($get('recommendation_decision'), [
                                RecommendationDecision::AppointPermanent->value,
                                RecommendationDecision::TransferOuttaskOutsource->value,
                                RecommendationDecision::ExtendOuttaskOutsource->value,
                            ], true)),
                        TextInput::make('extension_months')
                            ->label('Durasi Kontrak / Perpanjangan (Bulan)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(24)
                            ->suffix('bulan')
                            ->visible(fn (Get $get): bool => in_array($get('recommendation_decision'), [
                                RecommendationDecision::ExtendPkwt->value,
                                RecommendationDecision::TransferOuttaskOutsource->value,
                                RecommendationDecision::ExtendOuttaskOutsource->value,
                            ], true)),
                        Textarea::make('recommendation_reason')
                            ->label('Pertimbangan / Catatan Keputusan (PKWT, Outtask/Outsource, atau Alasan Terminasi)')
                            ->rows(2)
                            ->columnSpanFull(),
                        DateTimePicker::make('reviewer_signed_at')
                            ->label('Tahap 1: Tgl Approval Penilai / Atasan Langsung (Level 1)')
                            ->native(false),
                        DateTimePicker::make('employee_signed_at')
                            ->label('Tahap 2: Tgl Konfirmasi / Tanda Tangan Karyawan')
                            ->native(false),
                        DateTimePicker::make('gm_signed_at')
                            ->label('Tahap 3: Tgl Approval Atasan Penilai / Kepala Dept (Level 2)')
                            ->native(false),
                    ])
                    ->columns(2),
            ])
            ->columns(1);
    }
}
