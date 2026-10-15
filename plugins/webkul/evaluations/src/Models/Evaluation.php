<?php

namespace Webkul\Evaluation\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Throwable;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Database\Factories\EvaluationFactory;
use Webkul\Evaluation\Enums\EvaluationStatus;
use Webkul\Evaluation\Enums\EvaluationType;
use Webkul\Evaluation\Enums\RecommendationDecision;
use Webkul\Performance\Models\PerformancePlan;
use Webkul\Security\Models\User;
use Webkul\Support\Traits\BelongsToCompany;

class Evaluation extends Model
{
    use BelongsToCompany;
    use HasFactory;

    public const GOAL_PASS_THRESHOLD = 95.0;

    public const WORK_MANAGEMENT_PASS_THRESHOLD = 3.0;

    public const COMPETENCY_PASS_THRESHOLD = 90.0;

    protected $table = 'evaluations_evaluations';

    protected $fillable = [
        'company_id',
        'employee_id',
        'reviewer_id',
        'employee_nik',
        'join_date',
        'grade_level',
        'division_name',
        'department_name',
        'job_title_snapshot',
        'work_location_name',
        'title',
        'evaluation_type',
        'is_final_period',
        'period_start',
        'period_end',
        'performance_plan_id',
        'goal_achievement_percentage',
        'competencies_met_count',
        'competencies_total_count',
        'status',
        'summary',
        'strengths',
        'areas_for_improvement',
        'development_plan',
        'recommendation_decision',
        'permanent_effective_date',
        'extension_months',
        'recommendation_reason',
        'employee_comment',
        'employee_signed_at',
        'reviewer_signed_at',
        'gm_approver_id',
        'gm_signed_at',
        'director_approver_id',
        'director_signed_at',
    ];

    protected static function newFactory(): EvaluationFactory
    {
        return EvaluationFactory::new();
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'period_start'                => 'date',
            'period_end'                  => 'date',
            'join_date'                   => 'date',
            'permanent_effective_date'    => 'date',
            'is_final_period'             => 'boolean',
            'goal_achievement_percentage' => 'float',
            'competencies_met_count'      => 'integer',
            'competencies_total_count'    => 'integer',
            'extension_months'            => 'integer',
            'status'                      => EvaluationStatus::class,
            'evaluation_type'             => EvaluationType::class,
            'recommendation_decision'     => RecommendationDecision::class,
            'employee_signed_at'          => 'datetime',
            'reviewer_signed_at'          => 'datetime',
            'gm_signed_at'                => 'datetime',
            'director_signed_at'          => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'reviewer_id');
    }

    /**
     * Level 2 Hierarchical Approver (Atasan dari Penilai / Kepala Departemen) stored in gm_approver_id.
     */
    public function gmApprover(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'gm_approver_id');
    }

    public function directorApprover(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'director_approver_id');
    }

    public function performancePlan(): BelongsTo
    {
        return $this->belongsTo(PerformancePlan::class, 'performance_plan_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(EvaluationRating::class)->orderBy('sort')->orderBy('id');
    }

    /**
     * Determine whether an Employee record belongs to an Outsource / Outtask employment scheme.
     */
    public static function isEmployeeOutsourceOrOuttask(?Employee $employee): bool
    {
        if (! $employee) {
            return false;
        }

        $haystack = mb_strtolower(implode(' ', array_filter([
            $employee->employee_type,
            $employee->employmentType?->name,
            $employee->employmentType?->code,
            $employee->job_title,
        ])));

        foreach (['outsource', 'outsourcing', 'outtask', 'out-task', 'alih daya', 'vendor'] as $keyword) {
            if (str_contains($haystack, $keyword)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether this evaluation is for an Outsource / Outtask employee or workflow.
     */
    public function isOutsourceOrOuttask(): bool
    {
        if ($this->evaluation_type === EvaluationType::OutsourceOuttask) {
            return true;
        }

        if (in_array($this->recommendation_decision, [
            RecommendationDecision::ExtendOuttaskOutsource,
            RecommendationDecision::TerminateOuttaskOutsource,
        ], true)) {
            return true;
        }

        $employee = $this->relationLoaded('employee')
            ? $this->employee
            : ($this->employee_id ? Employee::withoutGlobalScopes()->with('employmentType')->find($this->employee_id) : null);

        return static::isEmployeeOutsourceOrOuttask($employee);
    }

    /**
     * Return the 10 standard PKWT work management aspects from the official PKWT Quarterly Evaluation form.
     *
     * @return array<int, array{title: string, description: string, weight: int, sort: int}>
     */
    public static function defaultPkwtCriteria(): array
    {
        return [
            [
                'sort'        => 1,
                'weight'      => 10,
                'title'       => 'Ketepatan hasil kerja dengan target dan batas waktu',
                'description' => 'Sejauh mana karyawan dapat menyelesaikan pekerjaan sesuai dengan target dan batas waktu yang telah ditetapkan.',
            ],
            [
                'sort'        => 2,
                'weight'      => 10,
                'title'       => 'Kualitas pekerjaan',
                'description' => 'Sejauh mana pekerjaan karyawan terselesaikan dengan baik, efektif, dan akurat.',
            ],
            [
                'sort'        => 3,
                'weight'      => 10,
                'title'       => 'Pemahaman mengenai pekerjaan',
                'description' => 'Sejauh mana karyawan memahami dan mengaplikasikan bagaimana dan mengapa perlu mengerjakan hal-hal yang menjadi tugasnya, sesuai dengan peran saat ini.',
            ],
            [
                'sort'        => 4,
                'weight'      => 10,
                'title'       => 'Hubungan kerja dengan atasan langsung',
                'description' => 'Sejauh mana karyawan menunjukkan sikap responsif terhadap arahan dan saran dari atasan langsung, serta sejauh mana ia proaktif mencari bimbingan sebagai cara untuk pengembangan diri dan peningkatan kinerja.',
            ],
            [
                'sort'        => 5,
                'weight'      => 10,
                'title'       => 'Kerjasama dengan karyawan lain',
                'description' => 'Sejauh mana karyawan dapat menjalin hubungan baik dengan sesama karyawan. Pertimbangkan aspek kepekaan, sopan santun, serta efektivitas komunikasinya ketika berinteraksi dengan rekan kerja, atasan, bawahan, maupun pelanggan.',
            ],
            [
                'sort'        => 6,
                'weight'      => 10,
                'title'       => 'Kehadiran dan kedisiplinan',
                'description' => 'Sejauh mana karyawan dapat hadir tepat waktu dan menunjukkan kedisiplinan yang konsisten, serta selalu menginformasikan kepada atasan langsung ketika akan datang terlambat atau berhalangan hadir.',
            ],
            [
                'sort'        => 7,
                'weight'      => 10,
                'title'       => 'Inisiatif dan kreativitas',
                'description' => 'Sejauh mana karyawan mampu mengelola pengerjaan tugasnya secara mandiri, serta menerapkan metode kreatif dalam mencapai target. Pertimbangkan seberapa baik karyawan dalam mengusulkan ide-ide baru untuk mempercepat proses atau memperbaiki hasil.',
            ],
            [
                'sort'        => 8,
                'weight'      => 10,
                'title'       => 'Keterbukaan untuk belajar dan berkembang',
                'description' => 'Sejauh mana karyawan menunjukkan kemampuan dan kesiapan untuk menerima tugas / tanggung jawab baru yang lebih kompleks.',
            ],
            [
                'sort'        => 9,
                'weight'      => 10,
                'title'       => 'Kemampuan berkomunikasi',
                'description' => 'Sejauh mana karyawan dapat menyampaikan ide atau pendapatnya secara efektif, baik secara formal maupun informal, dalam bentuk lisan maupun tertulis. Pertimbangkan juga kemampuan komunikasi verbal dan non-verbal (bahasa tubuh) karyawan saat presentasi.',
            ],
            [
                'sort'        => 10,
                'weight'      => 10,
                'title'       => 'Kemampuan mengambil keputusan dan menyelesaikan masalah',
                'description' => 'Sejauh mana karyawan dapat mengelola prioritas pekerjaan secara efektif, serta mempertimbangkan situasi / resiko sebelum bertindak.',
            ],
        ];
    }

    /**
     * Seed the 10 standard PKWT work management aspects onto this evaluation if not already present.
     */
    public function seedDefaultPkwtCriteria(bool $replaceExisting = false): int
    {
        if ($replaceExisting) {
            $this->ratings()->delete();
        }

        $existingTitles = $this->ratings()->pluck('title')->map(fn (string $t) => mb_strtolower(trim($t)))->all();
        $created = 0;

        foreach (static::defaultPkwtCriteria() as $criterion) {
            if (in_array(mb_strtolower(trim($criterion['title'])), $existingTitles, true)) {
                continue;
            }

            $this->ratings()->create($criterion);
            $created++;
        }

        $this->unsetRelation('ratings');

        return $created;
    }

    /**
     * Total sum of rating scores (Section D: Total Nilai / Sigma Rating).
     */
    public function totalRatingScore(): int
    {
        $ratings = $this->relationLoaded('ratings') ? $this->ratings : $this->ratings()->get();

        return (int) $ratings
            ->filter(fn (EvaluationRating $rating): bool => $rating->score !== null)
            ->sum('score');
    }

    /**
     * Weighted average score on a scale of 1 to 5 (Section D: Rata-rata Nilai).
     */
    public function finalScore(): ?float
    {
        $ratings = $this->relationLoaded('ratings') ? $this->ratings : $this->ratings()->get();
        $scored = $ratings->filter(fn (EvaluationRating $rating): bool => $rating->score !== null && $rating->weight > 0);
        $weight = (int) $scored->sum('weight');

        if ($weight <= 0) {
            return null;
        }

        $weighted = $scored->sum(fn (EvaluationRating $rating): float => $rating->score * $rating->weight);

        return round($weighted / $weight, 1);
    }

    /**
     * Competency achievement percentage (Section E & H: Jumlah kompetensi yang memenuhi requirement).
     */
    public function competencyAchievementPercentage(): ?float
    {
        if (! $this->competencies_total_count || $this->competencies_total_count <= 0) {
            return null;
        }

        $met = max(0, (int) $this->competencies_met_count);

        return round(($met / $this->competencies_total_count) * 100, 1);
    }

    /**
     * Resolve effective goal achievement percentage (returns null when Outsource/Outtask).
     */
    public function resolvedGoalAchievementPercentage(): ?float
    {
        if ($this->isOutsourceOrOuttask()) {
            return null;
        }

        if ($this->goal_achievement_percentage !== null) {
            return round((float) $this->goal_achievement_percentage, 1);
        }

        try {
            if ($this->performance_plan_id && class_exists(PerformancePlan::class) && Schema::hasTable('performance_plans')) {
                $plan = $this->relationLoaded('performancePlan')
                    ? $this->performancePlan
                    : PerformancePlan::withoutGlobalScopes()->find($this->performance_plan_id);

                return $plan?->weightedScore();
            }
        } catch (Throwable) {
        }

        return null;
    }

    /**
     * Sync goal achievement percentage from linked or matching PerformancePlan.
     */
    public function syncGoalFromPerformancePlan(): ?float
    {
        if ($this->isOutsourceOrOuttask()) {
            return null;
        }

        try {
            if (! class_exists(PerformancePlan::class) || ! Schema::hasTable('performance_plans')) {
                return null;
            }

            $plan = null;

            if ($this->performance_plan_id) {
                $plan = PerformancePlan::withoutGlobalScopes()->find($this->performance_plan_id);
            }

            if (! $plan && $this->employee_id) {
                $plan = PerformancePlan::withoutGlobalScopes()
                    ->where('employee_id', $this->employee_id)
                    ->orderByDesc('period_start')
                    ->first();
            }

            if ($plan) {
                $score = $plan->weightedScore();
                $this->performance_plan_id = $plan->id;

                if ($score !== null) {
                    $this->goal_achievement_percentage = $score;
                }

                $this->saveQuietly();

                return $score;
            }
        } catch (Throwable) {
        }

        return null;
    }

    /**
     * Criterion 1 (Section I): Persentase minimum pencapaian goal adalah 95%.
     */
    public function meetsGoalCriterion(): bool
    {
        $goal = $this->resolvedGoalAchievementPercentage();

        return $goal !== null && $goal >= self::GOAL_PASS_THRESHOLD;
    }

    /**
     * Criterion 2 (Section I): Nilai rata-rata minimum bagian manajemen pekerjaan (kolom D) adalah 3.0.
     */
    public function meetsWorkManagementCriterion(): bool
    {
        $score = $this->finalScore();

        return $score !== null && $score >= self::WORK_MANAGEMENT_PASS_THRESHOLD;
    }

    /**
     * Criterion 3 (Section I): Persentase minimum kompetensi yang memenuhi requirement adalah 90%.
     */
    public function meetsCompetencyCriterion(): bool
    {
        $comp = $this->competencyAchievementPercentage();

        return $comp !== null && $comp >= self::COMPETENCY_PASS_THRESHOLD;
    }

    /**
     * Number of permanent-appointment criteria met.
     */
    public function criteriaMetCount(): int
    {
        if ($this->isOutsourceOrOuttask()) {
            return (int) $this->meetsWorkManagementCriterion()
                + (int) $this->meetsCompetencyCriterion();
        }

        return (int) $this->meetsGoalCriterion()
            + (int) $this->meetsWorkManagementCriterion()
            + (int) $this->meetsCompetencyCriterion();
    }

    /**
     * Employee is eligible if at least 2 criteria are met.
     */
    public function isEligibleForPermanentAppointment(): bool
    {
        return $this->criteriaMetCount() >= 2;
    }

    /**
     * Structured summary of Section H & I criteria for UI badges and PDF output.
     *
     * @return array{
     *     is_outsource: bool,
     *     goal_percentage: ?float,
     *     goal_met: bool,
     *     work_management_score: ?float,
     *     work_management_met: bool,
     *     competency_percentage: ?float,
     *     competency_met: bool,
     *     criteria_met_count: int,
     *     eligible_for_permanent: bool
     * }
     */
    public function criteriaSummary(): array
    {
        return [
            'is_outsource'           => $this->isOutsourceOrOuttask(),
            'goal_percentage'        => $this->resolvedGoalAchievementPercentage(),
            'goal_met'               => $this->meetsGoalCriterion(),
            'work_management_score'  => $this->finalScore(),
            'work_management_met'    => $this->meetsWorkManagementCriterion(),
            'competency_percentage'  => $this->competencyAchievementPercentage(),
            'competency_met'         => $this->meetsCompetencyCriterion(),
            'criteria_met_count'     => $this->criteriaMetCount(),
            'eligible_for_permanent' => $this->isEligibleForPermanentAppointment(),
        ];
    }

    /**
     * Determine current stage label in the hierarchical approval workflow:
     * Tahap 1: Penilai (Atasan Langsung - L1) -> Tahap 2: Karyawan (Konfirmasi Hasil Diskusi) -> Tahap 3: Atasan Penilai / Kepala Dept (L2) jika ditetapkan.
     */
    public function approvalStageLabel(): string
    {
        if (! $this->reviewer_signed_at) {
            return 'Menunggu Penilai / Atasan Langsung (Level 1)';
        }

        if (! $this->employee_signed_at) {
            return 'Menunggu Konfirmasi Karyawan';
        }

        if ($this->gm_approver_id && ! $this->gm_signed_at) {
            return 'Menunggu Atasan Penilai / Kepala Dept (Level 2)';
        }

        return 'Persetujuan Lengkap (Selesai)';
    }

    /**
     * Determine whether the user can sign/approve this evaluation at any stage (Reviewer L1, Employee, or Approver L2),
     * or if the user has update permission on evaluations.
     */
    public function canBeSignedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        if ($user->can('update_evaluation_evaluation')) {
            return true;
        }

        $empIds = Employee::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        $allowedSigners = array_map('intval', array_filter([
            $this->employee_id,
            $this->reviewer_id,
            $this->gm_approver_id,
            $this->director_approver_id,
        ]));

        return ! empty(array_intersect($empIds, $allowedSigners));
    }

    /**
     * Execute sequential/tiered approval by role or automatically detect from the given User.
     */
    public function signByRole(string $role, ?User $user = null): string
    {
        $now = now();
        $empIds = $user
            ? Employee::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->pluck('id')
                ->map(fn ($id) => (int) $id)
                ->all()
            : [];

        $isAdminOrHrd = $user && $user->can('update_evaluation_evaluation');

        if ($role === 'auto' && $user) {
            if (! $this->reviewer_signed_at && in_array((int) $this->reviewer_id, $empIds, true)) {
                $role = 'reviewer';
            } elseif (! $this->employee_signed_at && in_array((int) $this->employee_id, $empIds, true)) {
                $role = 'employee';
            } elseif ($this->gm_approver_id && ! $this->gm_signed_at && in_array((int) $this->gm_approver_id, $empIds, true)) {
                $role = 'level2';
            } elseif ($isAdminOrHrd) {
                if (! $this->reviewer_signed_at) {
                    $role = 'reviewer';
                } elseif (! $this->employee_signed_at) {
                    $role = 'employee';
                } else {
                    $role = 'level2';
                }
            } elseif (in_array((int) $this->employee_id, $empIds, true)) {
                $role = 'employee';
            } elseif (in_array((int) $this->reviewer_id, $empIds, true)) {
                $role = 'reviewer';
            } elseif (in_array((int) $this->gm_approver_id, $empIds, true)) {
                $role = 'level2';
            }
        } elseif (! $isAdminOrHrd && $user) {
            if ($role === 'reviewer' && ! in_array((int) $this->reviewer_id, $empIds, true)) {
                $role = in_array((int) $this->employee_id, $empIds, true) ? 'employee' : $role;
            } elseif ($role === 'level2' && ! in_array((int) $this->gm_approver_id, $empIds, true)) {
                $role = in_array((int) $this->employee_id, $empIds, true) ? 'employee' : $role;
            }
        }

        match ($role) {
            'employee' => $this->employee_signed_at = $now,
            'level2'   => $this->gm_signed_at = $now,
            default    => $this->reviewer_signed_at = $now,
        };

        $this->recalculateStatusFromSignatures();
        $this->save();

        return $this->approvalStageLabel();
    }

    public function recalculateStatusFromSignatures(): void
    {
        $l2Complete = ! $this->gm_approver_id || $this->gm_signed_at !== null;

        if ($this->reviewer_signed_at && $this->employee_signed_at && $l2Complete) {
            $this->status = EvaluationStatus::Completed;
        } else {
            $this->status = EvaluationStatus::InReview;
        }
    }

    public function periodLabel(): string
    {
        $start = $this->period_start?->format('d M Y') ?? '—';
        $end = $this->period_end?->format('d M Y') ?? '—';

        return $start.' – '.$end;
    }

    public function populateEmployeeMetadata(): void
    {
        if (! $this->employee_id) {
            return;
        }

        $employee = Employee::withoutGlobalScopes()
            ->with(['department', 'workLocation', 'job', 'employmentType'])
            ->find($this->employee_id);

        if (! $employee) {
            return;
        }

        if ($employee->company_id) {
            $this->company_id = $employee->company_id;
        }

        if (static::isEmployeeOutsourceOrOuttask($employee) && ($this->evaluation_type === null || $this->evaluation_type === EvaluationType::PkwtQ1)) {
            $this->evaluation_type = EvaluationType::OutsourceOuttask;
        }

        if ($this->isOutsourceOrOuttask()) {
            $this->performance_plan_id = null;
            $this->goal_achievement_percentage = null;
        }

        $levelOne = $employee->resolveLevelOneApproverEmployee();
        $levelTwo = $employee->resolveLevelTwoApproverEmployee();

        $this->reviewer_id ??= $levelOne?->id ?? $employee->parent_id;
        $this->gm_approver_id ??= $levelTwo?->id;
        $this->employee_nik ??= $employee->identification_id;
        $this->join_date ??= $employee->join_date;
        $this->department_name ??= $employee->department?->name;
        $this->job_title_snapshot ??= $employee->job_title ?: $employee->job?->name;
        $this->work_location_name ??= $employee->workLocation?->name;

        if ($this->evaluation_type === EvaluationType::PkwtFinal) {
            $this->is_final_period = true;
        }
    }

    protected static function booted(): void
    {
        static::saving(function (Evaluation $evaluation): void {
            $evaluation->populateEmployeeMetadata();
        });
    }
}
