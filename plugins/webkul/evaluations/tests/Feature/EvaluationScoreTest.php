<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Barryvdh\DomPDF\Facade\Pdf;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Enums\EvaluationType;
use Webkul\Evaluation\Enums\RecommendationDecision;
use Webkul\Evaluation\Models\Evaluation;
use Webkul\Evaluation\Models\EvaluationRating;
use Webkul\Performance\Models\PerformanceGoal;
use Webkul\Performance\Models\PerformancePlan;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('performance');
    TestBootstrapHelper::ensurePluginInstalled('evaluations');

    $pkwtMigration = include __DIR__.'/../../database/migrations/2026_10_15_140000_add_pkwt_quarterly_fields_to_evaluations_tables.php';
    $pkwtMigration->up();
});

it('calculates a weighted score on a scale of 1 to 5', function () {
    $employee = Employee::withoutGlobalScopes()->create([
        'name'      => 'Uji Evaluasi',
        'is_active' => true,
    ]);

    $evaluation = Evaluation::factory()->create([
        'employee_id' => $employee->id,
        'company_id'  => $employee->company_id,
    ]);

    EvaluationRating::factory()->create([
        'evaluation_id' => $evaluation->id,
        'weight'        => 60,
        'score'         => 4,
        'sort'          => 1,
    ]);

    EvaluationRating::factory()->create([
        'evaluation_id' => $evaluation->id,
        'weight'        => 40,
        'score'         => 5,
        'sort'          => 2,
    ]);

    expect($evaluation->fresh()->finalScore())->toEqual(4.4);
});

it('returns no score when every criterion has zero weight', function () {
    $employee = Employee::withoutGlobalScopes()->create([
        'name'      => 'Uji Evaluasi Nol',
        'is_active' => true,
    ]);

    $evaluation = Evaluation::factory()->create([
        'employee_id' => $employee->id,
        'company_id'  => $employee->company_id,
    ]);

    EvaluationRating::factory()->create([
        'evaluation_id' => $evaluation->id,
        'weight'        => 0,
        'score'         => 5,
    ]);

    expect($evaluation->fresh()->finalScore())->toBeNull();
});

it('ignores criteria that have not been scored', function () {
    $employee = Employee::withoutGlobalScopes()->create([
        'name'      => 'Uji Evaluasi Kosong',
        'is_active' => true,
    ]);

    $evaluation = Evaluation::factory()->create([
        'employee_id' => $employee->id,
        'company_id'  => $employee->company_id,
    ]);

    EvaluationRating::factory()->create([
        'evaluation_id' => $evaluation->id,
        'weight'        => 50,
        'score'         => null,
    ]);

    EvaluationRating::factory()->create([
        'evaluation_id' => $evaluation->id,
        'weight'        => 50,
        'score'         => 5,
    ]);

    expect($evaluation->fresh()->finalScore())->toEqual(5.0);
});

it('seeds the 10 standard PKWT work management aspects, syncs Goal score, evaluates 2-of-3 permanent appointment criteria, and renders the PKWT PDF', function () {
    $manager = Employee::withoutGlobalScopes()->create([
        'name'              => 'Atasan Penilai PKWT',
        'identification_id' => 'NIK-MGR-001',
        'is_active'         => true,
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'              => 'Karyawan PKWT Triwulan',
        'identification_id' => 'NIK-PKWT-099',
        'job_title'         => 'Software Engineer',
        'parent_id'         => $manager->id,
        'join_date'         => now()->subMonths(9)->toDateString(),
        'is_active'         => true,
    ]);

    $plan = PerformancePlan::withoutGlobalScopes()->create([
        'employee_id'  => $employee->id,
        'title'        => 'Goal Setting PKWT 2026',
        'period_start' => now()->startOfQuarter()->toDateString(),
        'period_end'   => now()->endOfQuarter()->toDateString(),
        'status'       => 'active',
    ]);

    PerformanceGoal::query()->create([
        'performance_plan_id' => $plan->id,
        'title'               => 'Penyelesaian Modul ERP',
        'weight'              => 100,
        'progress'            => 96,
        'status'              => 'achieved',
    ]);

    $evaluation = Evaluation::query()->create([
        'employee_id'              => $employee->id,
        'title'                    => 'Evaluasi Akhir PKWT - Triwulan IV',
        'evaluation_type'          => EvaluationType::PkwtFinal,
        'period_start'             => now()->startOfQuarter()->toDateString(),
        'period_end'               => now()->endOfQuarter()->toDateString(),
        'performance_plan_id'      => $plan->id,
        'competencies_met_count'   => 8,
        'competencies_total_count' => 10, // 80% (< 90% threshold)
        'strengths'                => 'Disiplin tinggi dan eksekusi tugas sangat cepat.',
        'areas_for_improvement'    => 'Presentasi di depan publik.',
        'development_plan'         => 'Ikuti pelatihan komunikasi efektif.',
        'recommendation_decision'  => RecommendationDecision::AppointPermanent,
        'permanent_effective_date' => now()->addMonth()->startOfMonth()->toDateString(),
    ]);

    // 1. Metadata auto-populated from Employee
    expect($evaluation->reviewer_id)->toBe($manager->id)
        ->and($evaluation->employee_nik)->toBe('NIK-PKWT-099')
        ->and($evaluation->job_title_snapshot)->toBe('Software Engineer')
        ->and($evaluation->is_final_period)->toBeTrue();

    // 2. Seed 10 standard PKWT work management aspects
    $addedCount = $evaluation->seedDefaultPkwtCriteria();
    expect($addedCount)->toBe(10)
        ->and($evaluation->ratings()->count())->toBe(10);

    // Score all 10 aspects with 4 (average = 4.0 >= 3.0, total = 40)
    $evaluation->ratings()->update(['score' => 4, 'comment' => 'Melebihi standar yang ditetapkan']);
    $evaluation->refresh();

    // 3. Sync Goal score from PerformancePlan (96% >= 95%)
    $syncedGoal = $evaluation->syncGoalFromPerformancePlan();
    expect($syncedGoal)->toEqual(96.0)
        ->and($evaluation->totalRatingScore())->toBe(40)
        ->and($evaluation->finalScore())->toEqual(4.0)
        ->and($evaluation->competencyAchievementPercentage())->toEqual(80.0);

    // 4. Verify 2-of-3 Criteria Calculation (Goal = 96% [PASS], Work Management = 4.0 [PASS], Competency = 80% [FAIL] => 2/3 PASS => ELIGIBLE!)
    expect($evaluation->meetsGoalCriterion())->toBeTrue()
        ->and($evaluation->meetsWorkManagementCriterion())->toBeTrue()
        ->and($evaluation->meetsCompetencyCriterion())->toBeFalse()
        ->and($evaluation->criteriaMetCount())->toBe(2)
        ->and($evaluation->isEligibleForPermanentAppointment())->toBeTrue();

    // 5. Verify PDF generation renders cleanly
    $evaluation->load(['employee', 'reviewer', 'performancePlan', 'ratings']);
    $pdfOutput = Pdf::loadView('evaluations::pdf.pkwt-evaluation', ['record' => $evaluation])->output();

    expect($pdfOutput)->toStartWith('%PDF-');
});
