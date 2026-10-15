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

it('syncs Goal score, supports Outtask/Outsource recommendation decision, 2-party approval (Employee & Reviewer), and renders the PDF cleanly', function () {
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
        'competencies_met_count'   => 9,
        'competencies_total_count' => 10,
        'strengths'                => 'Disiplin tinggi dan eksekusi tugas sangat cepat.',
        'areas_for_improvement'    => 'Presentasi di depan publik.',
        'development_plan'         => 'Ikuti pelatihan komunikasi efektif.',
        'recommendation_decision'  => RecommendationDecision::TransferOuttaskOutsource,
        'permanent_effective_date' => now()->addMonth()->startOfMonth()->toDateString(),
        'extension_months'         => 12,
        'recommendation_reason'    => 'Penyesuaian alokasi fungsi operasional ke skema Outtask / Outsource.',
        'employee_signed_at'       => now(),
        'reviewer_signed_at'       => now(),
    ]);

    // 1. Metadata auto-populated from Employee and no 10-point default ratings auto-seeded
    expect($evaluation->reviewer_id)->toBe($manager->id)
        ->and($evaluation->employee_nik)->toBe('NIK-PKWT-099')
        ->and($evaluation->job_title_snapshot)->toBe('Software Engineer')
        ->and($evaluation->is_final_period)->toBeTrue()
        ->and($evaluation->ratings()->count())->toBe(0)
        ->and($evaluation->recommendation_decision)->toBe(RecommendationDecision::TransferOuttaskOutsource)
        ->and($evaluation->recommendation_decision->getLabel())->toBe('Dialihkan ke Outtask / Outsource');

    // 2. Sync Goal score from PerformancePlan (96% >= 95%) and Competency (90% >= 90%)
    $syncedGoal = $evaluation->syncGoalFromPerformancePlan();
    expect($syncedGoal)->toEqual(96.0)
        ->and($evaluation->competencyAchievementPercentage())->toEqual(90.0)
        ->and($evaluation->meetsGoalCriterion())->toBeTrue()
        ->and($evaluation->meetsCompetencyCriterion())->toBeTrue();

    // 3. Verify PDF generation renders cleanly with Outtask/Outsource decision and 2-party signatures
    $evaluation->load(['employee', 'reviewer', 'performancePlan', 'ratings']);
    $pdfOutput = Pdf::loadView('evaluations::pdf.pkwt-evaluation', ['record' => $evaluation])->output();

    expect($pdfOutput)->toStartWith('%PDF-');
});
