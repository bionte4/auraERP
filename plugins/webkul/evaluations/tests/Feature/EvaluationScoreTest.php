<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Barryvdh\DomPDF\Facade\Pdf;
use Webkul\Employee\Models\Employee;
use Webkul\Evaluation\Enums\EvaluationStatus;
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

it('supports all 6 recommendation decisions (including Extend/Terminate Outtask/Outsource) and hierarchical L1->Employee->L2 approval routing', function () {
    $deptHeadL2 = Employee::withoutGlobalScopes()->create([
        'name'              => 'Kepala Departemen (Approver L2)',
        'identification_id' => 'NIK-HEAD-001',
        'is_active'         => true,
    ]);

    $managerL1 = Employee::withoutGlobalScopes()->create([
        'name'              => 'Atasan Penilai (Approver L1)',
        'identification_id' => 'NIK-MGR-001',
        'parent_id'         => $deptHeadL2->id,
        'is_active'         => true,
    ]);

    $employee = Employee::withoutGlobalScopes()->create([
        'name'              => 'Karyawan PKWT Triwulan',
        'identification_id' => 'NIK-PKWT-099',
        'job_title'         => 'Software Engineer',
        'parent_id'         => $managerL1->id,
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

    expect(RecommendationDecision::options())->toBe([
        'appoint_permanent'           => 'Diangkat Sebagai Karyawan Tetap',
        'extend_pkwt'                 => 'Diperpanjang Masa PKWT',
        'transfer_outtask_outsource'  => 'Dialihkan ke Outtask / Outsource',
        'extend_outtask_outsource'    => 'Diperpanjang Masa Outtask/Outsource',
        'terminate_outtask_outsource' => 'Tidak Diperpanjang Outtask/Outsource',
        'terminate_pkwt'              => 'Tidak Direkomendasikan / Terminasi PKWT',
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
        'recommendation_decision'  => RecommendationDecision::ExtendOuttaskOutsource,
        'permanent_effective_date' => now()->addMonth()->startOfMonth()->toDateString(),
        'extension_months'         => 6,
        'recommendation_reason'    => 'Perpanjangan masa kontrak Outtask/Outsource selama 6 bulan.',
    ]);

    // 1. Hierarchical Approver L1 & L2 auto-populated from Employee org-tree
    $addedCount = $evaluation->seedDefaultPkwtCriteria();
    expect($evaluation->reviewer_id)->toBe($managerL1->id)
        ->and($evaluation->gm_approver_id)->toBe($deptHeadL2->id)
        ->and($addedCount)->toBe(10)
        ->and($evaluation->approvalStageLabel())->toBe('Menunggu Penilai / Atasan Langsung (Level 1)');

    // 2. Step-by-step tiered approval (L1 Reviewer -> Employee -> L2 Dept Head)
    $evaluation->signByRole('reviewer');
    expect($evaluation->status)->toBe(EvaluationStatus::InReview)
        ->and($evaluation->approvalStageLabel())->toBe('Menunggu Konfirmasi Karyawan');

    $evaluation->signByRole('employee');
    expect($evaluation->status)->toBe(EvaluationStatus::InReview)
        ->and($evaluation->approvalStageLabel())->toBe('Menunggu Atasan Penilai / Kepala Dept (Level 2)');

    $evaluation->signByRole('level2');
    expect($evaluation->status)->toBe(EvaluationStatus::Completed)
        ->and($evaluation->approvalStageLabel())->toBe('Persetujuan Lengkap (Selesai)');

    // 3. Verify PDF generation renders cleanly with all 6 recommendation options and 3-column tiered approval
    $evaluation->load(['employee', 'reviewer', 'gmApprover', 'performancePlan', 'ratings']);
    $pdfOutput = Pdf::loadView('evaluations::pdf.pkwt-evaluation', ['record' => $evaluation])->output();

    expect($pdfOutput)->toStartWith('%PDF-')
        ->and($evaluation->isOutsourceOrOuttask())->toBeTrue()
        ->and($evaluation->resolvedGoalAchievementPercentage())->toBeNull();
});
